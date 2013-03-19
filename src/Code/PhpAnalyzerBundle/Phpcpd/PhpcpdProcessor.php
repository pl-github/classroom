<?php

namespace Code\PhpAnalyzerBundle\Phpcpd;

use Code\AnalyzerBundle\Analyzer\Processor\ProcessorInterface;
use Code\AnalyzerBundle\Metric\Metric;
use Code\AnalyzerBundle\Smell\Smell;
use Code\AnalyzerBundle\Source\SourceRange;
use Code\AnalyzerBundle\ReflectionService;
use Code\AnalyzerBundle\Model\ResultModel;

class PhpcpdProcessor implements ProcessorInterface
{
    /**
     * @inheritDoc
     */
    public function process(ResultModel $result, $filename)
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
                $fileNode = $result->getNode($path);
                $classNode = $result->getNode(current($result->getIncoming('node', $fileNode)));

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
