<?php

namespace Code\PhpAnalyzerBundle\Phpcpd;

use Code\AnalyzerBundle\Processor\ProcessorInterface;
use Code\AnalyzerBundle\ReflectionService;
use Code\AnalyzerBundle\Result\Metric\Metric;
use Code\AnalyzerBundle\Result\Result;
use Code\AnalyzerBundle\Result\Smell\Smell;
use Code\AnalyzerBundle\Result\Source\SourceRange;

class PhpcpdProcessor implements ProcessorInterface
{
    /**
     * @var PhpcpdCollector
     */
    private $collector;

    /**
     * @param PhpcpdCollector $collector
     */
    public function __construct(PhpcpdCollector $collector)
    {
        $this->collector = $collector;
    }

    /**
     * @inheritDoc
     */
    public function process(Result $result)
    {
        $filename = $this->collector->collect(
            $result->getLog(),
            $result->getSourceDirectory(),
            $result->getWorkingDirectory()
        );

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
                $fileNode = $result->getNode($path);
                $classNode = $result->getNode(current($result->getReference('node', 'children', $fileNode)));

                $metric = new Metric('duplication', $lines);
                $classNode->addMetric($metric);

                $beginLine = $line;
                $endLine = $line + $lines + 1;
                $sourceRange = new SourceRange($beginLine, $endLine);

                $smell = new Smell(
                    'Duplication',
                    'Duplication',
                    'Similar code in ' . $fileCount . ' files.',
                    $sourceRange,
                    1
                );
                $result->addSmell($smell, $classNode);
            }
        }

        $result->addArtifact($filename);
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
