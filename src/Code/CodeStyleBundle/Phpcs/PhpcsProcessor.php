<?php

namespace Code\CodeStyleBundle\Phpcs;

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

class PhpcsProcessor implements ProcessorInterface
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
            throw new \Exception('phpcs report xml file not found.');
        }

        $xml = simplexml_load_file($filename);

        foreach ($xml->file as $fileNode) {
            $fileAttributes = $fileNode->attributes();

            $name = (string)$fileAttributes['name'];
            //$errors = (string)$fileAttributes['errors'];
            //$warning = (string)$fileAttributes['warnings'];

            $fileResultNode = $resultBuilder->getNode($name);
            $classResultReference = current($resultBuilder->getIncomingReferences($fileResultNode));

            if (isset($fileNode->warning)) {
                foreach ($fileNode->warning as $warningNode) {
                    $warningAttributes = $warningNode->attributes();

                    $line = (string)$warningAttributes['line'];
                    //$column = (string)$warningAttributes['column'];
                    //$source = (string)$warningAttributes['source'];
                    //$severity = (string)$warningAttributes['severity'];
                    $message = (string)$warningNode;

                    $sourceRange = new SourceRange($line);

                    $smell = new SmellModel($classResultReference, 'CodeStyle', 'CodeStyleWarning', $message, $sourceRange, 1);
                    $resultBuilder->addSmell($smell);
                }
            }

            if (isset($fileNode->error)) {
                foreach ($fileNode->error as $errorNode) {
                    $errorAttributes = $errorNode->attributes();

                    $line = (string)$errorAttributes['line'];
                    //$column = (string)$errorAttributes['column'];
                    //$source = (string)$errorAttributes['source'];
                    //$severity = (string)$errorAttributes['severity'];
                    $message = (string)$errorNode;

                    $sourceRange = new SourceRange($line);

                    $smell = new SmellModel($classResultReference, 'CodeStyle', 'CodeStyleError', $message, $sourceRange, 1);
                    $resultBuilder->addSmell($smell);
                }
            }
        }
    }
}
