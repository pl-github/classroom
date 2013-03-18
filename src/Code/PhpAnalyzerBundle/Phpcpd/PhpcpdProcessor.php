<?php

namespace Code\CopyPasteDetectionBundle\Phpcpd;

use Code\AnalyzerBundle\Analyzer\Processor\ProcessorInterface;
use Code\AnalyzerBundle\Model\NodeReference;
use Code\AnalyzerBundle\Model\SourceModel;
use Code\AnalyzerBundle\Model\SourceRange;
use Code\AnalyzerBundle\ReflectionService;
use Code\AnalyzerBundle\Model\ClassesModel;
use Code\AnalyzerBundle\Model\ClassModel;
use Code\AnalyzerBundle\Model\MetricModel;
use Code\AnalyzerBundle\Model\SmellModel;
use Code\AnalyzerBundle\ResultBuilder;

class PhpcpdProcessor implements ProcessorInterface
{
    /**
     * @var ReflectionService
     */
    private $reflectionService;

    /**
     * @param ReflectionService $reflectionService
     */
    public function __construct(ReflectionService $reflectionService)
    {
        $this->reflectionService = $reflectionService;
    }

    /**
     * @inheritDoc
     */
    public function process(ResultBuilder $resultBuilder, $filename)
    {
        if (!file_exists($filename)) {
            throw new \Exception('phpcpd log xml file not found.');
        }

        $duplications = $this->processDuplications($filename);

        foreach ($duplications as $duplication) {
            $fileCount = count($duplication['files']);

            $lines = $duplication['lines'];

            $files = array();
            foreach ($duplication['files'] as $path => $line) {
                $files[] = array(
                    'file'      => $path,
                    'startLine' => $line,
                    'endLine'   => $line + $lines + 1
                );
            }

            foreach ($duplication['files'] as $path => $line) {
                //$className = $this->reflectionService->getClassNameForFile($path);
                //$namespaceName = $this->reflectionService->getNamespaceNameForFile($path);

                $fileResultNode = $resultBuilder->getNode($path);
                $classResultReference = current($resultBuilder->getIncomingReferences($fileResultNode));

                $metric = new MetricModel('duplication', $lines);
                $fileResultNode->addMetric($metric);

                $beginLine = $line;
                $endLine = $line + $lines + 1;
                $sourceRange = new SourceRange($beginLine, $endLine);

                $smell = new SmellModel(
                    $classResultReference,
                    'Duplication',
                    'Duplication',
                    'Similar code in ' . $fileCount . ' files.',
                    $sourceRange,
                    1
                );
                $resultBuilder->addSmell($smell);
            }
        }
    }

    private function processDuplications($filename)
    {
        return $this->mergeDuplications($this->readDuplications($filename));
    }

    private function readDuplications($filename)
    {
        $xml = simplexml_load_file($filename);

        $duplications = array();
        foreach ($xml->duplication as $duplicationNode) {
            $duplAttributes = $duplicationNode->attributes();
            $lines = (string)$duplAttributes['lines'];
            $tokens = (string)$duplAttributes['tokens'];

            $files = array();
            foreach ($duplicationNode->file as $fileNode) {
                $fileAttributes = $fileNode->attributes();
                $path = (string)$fileAttributes['path'];
                $line = (string)$fileAttributes['line'];

                $key = $path.'_'.$line.'_'.$lines.'_'.$tokens;
                $files[$key] = array(
                    'key'    => $key,
                    'path'   => $path,
                    'line'   => $line,
                    'lines'  => $lines,
                    'tokens' => $tokens,
                    'target' => null,
                );
            }

            foreach (array_keys($files) as $key) {
                $targets = $files;
                unset($targets[$key]);
                reset($targets);
                $target = key($targets);
                $files[$key]['target'] = $target;
            }

            $duplications = array_merge($duplications, $files);
        }

        return $duplications;
    }

    private function mergeDuplications($duplications)
    {
        $mergedDuplications = array();
        while (count($duplications)) {
            $currentDuplication = array_shift($duplications);
            $key = $currentDuplication['key'];

            $target = $currentDuplication['target'];
            $item = array(
                'lines' => $currentDuplication['lines'],
                'tokens' => $currentDuplication['tokens'],
                'files' => array(
                    $currentDuplication['path'] => $currentDuplication['line'],
                    $duplications[$target]['path'] => $duplications[$target]['line'],
                ),
            );
            unset($duplications[$target]);

            foreach ($duplications as $checkId => $checkDuplication) {
                if ($checkDuplication['target'] === $key) {
                    $item['files'][$checkDuplication['path']] = $currentDuplication['line'];
                    unset($duplications[$checkId]);
                }
            }

            $mergedDuplications[] = $item;
        }

        return $mergedDuplications;
    }
}
