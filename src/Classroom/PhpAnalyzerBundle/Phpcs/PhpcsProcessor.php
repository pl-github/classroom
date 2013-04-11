<?php

namespace Classroom\PhpAnalyzerBundle\Phpcs;

use Classroom\AnalyzerBundle\Processor\ProcessorInterface;
use Classroom\AnalyzerBundle\ReflectionService;
use Classroom\AnalyzerBundle\Result\Result;
use Classroom\AnalyzerBundle\Result\Smell\Smell;
use Classroom\AnalyzerBundle\Result\Source\SourceRange;
use Classroom\PhpAnalyzerBundle\Node\PhpFileNode;

class PhpcsProcessor implements ProcessorInterface
{
    /**
     * @var PhpcsCollector
     */
    private $collector;

    /**
     * @param PhpcsCollector $collector
     */
    public function __construct(PhpcsCollector $collector)
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

        $xml = simplexml_load_file($filename);

        foreach ($xml->file as $xmlFileNode) {
            $fileAttributes = $xmlFileNode->attributes();

            $name = (string)$fileAttributes['name'];
            //$errors = (string)$fileAttributes['errors'];
            //$warning = (string)$fileAttributes['warnings'];

            $fileNode = $result->getNode(new PhpFileNode($name));
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
