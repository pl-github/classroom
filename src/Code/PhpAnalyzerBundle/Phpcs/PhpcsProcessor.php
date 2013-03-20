<?php

namespace Code\PhpAnalyzerBundle\Phpcs;

use Code\AnalyzerBundle\Analyzer\Processor\ProcessorInterface;
use Code\AnalyzerBundle\Model\ResultModel;
use Code\AnalyzerBundle\ReflectionService;
use Code\AnalyzerBundle\Smell\Smell;
use Code\AnalyzerBundle\Source\SourceRange;

class PhpcsProcessor implements ProcessorInterface
{
    /**
     * @inheritDoc
     */
    public function process(ResultModel $result, $filename)
    {
        if (!file_exists($filename)) {
            throw new \Exception('phpcs report xml file not found.');
        }

        $xml = simplexml_load_file($filename);

        foreach ($xml->file as $xmlFileNode) {
            $fileAttributes = $xmlFileNode->attributes();

            $name = (string)$fileAttributes['name'];
            //$errors = (string)$fileAttributes['errors'];
            //$warning = (string)$fileAttributes['warnings'];

            $fileNode = $result->getNode($name);
            $classNode = $result->getNode(current($result->getReference('node', 'children', $fileNode)));

            if (isset($xmlFileNode->warning)) {
                foreach ($xmlFileNode->warning as $xmlWarningNode) {
                    $warningAttributes = $xmlWarningNode->attributes();

                    $line = (string)$warningAttributes['line'];
                    //$column = (string)$warningAttributes['column'];
                    //$source = (string)$warningAttributes['source'];
                    //$severity = (string)$warningAttributes['severity'];
                    $message = (string)$xmlWarningNode;

                    $sourceRange = new SourceRange($line);

                    $smell = new Smell(
                        'CodeStyle',
                        'CodeStyleWarning',
                        $message,
                        $sourceRange,
                        1
                    );
                    $result->addSmell($smell, $classNode);
                }
            }

            if (isset($xmlFileNode->error)) {
                foreach ($xmlFileNode->error as $errorNode) {
                    $errorAttributes = $errorNode->attributes();

                    $line = (string)$errorAttributes['line'];
                    //$column = (string)$errorAttributes['column'];
                    //$source = (string)$errorAttributes['source'];
                    //$severity = (string)$errorAttributes['severity'];
                    $message = (string)$errorNode;

                    $sourceRange = new SourceRange($line);

                    $smell = new Smell(
                        'CodeStyle',
                        'CodeStyleError',
                        $message,
                        $sourceRange,
                        2
                    );
                    $result->addSmell($smell, $classNode);
                }
            }
        }

        $result->addArtifact($filename);
    }
}
