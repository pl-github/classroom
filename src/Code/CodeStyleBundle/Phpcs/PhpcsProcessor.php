<?php

namespace Code\CodeStyleBundle\Phpcs;

use Code\AnalyzerBundle\Analyzer\Processor\ProcessorInterface;
use Code\AnalyzerBundle\Model\SourceModel;
use Code\AnalyzerBundle\ReflectionService;
use Code\AnalyzerBundle\Model\ClassesModel;
use Code\AnalyzerBundle\Model\ClassModel;
use Code\AnalyzerBundle\Model\MetricModel;
use Code\AnalyzerBundle\Model\SmellModel;

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
    public function process($filename)
    {
        $xml = simplexml_load_file($filename);

        $classes = new ClassesModel();
        foreach ($xml->file as $fileNode) {
            $fileAttributes = $fileNode->attributes();

            $name = (string)$fileAttributes['name'];
            $errors = (string)$fileAttributes['errors'];
            $warning = (string)$fileAttributes['warnings'];

            $className = $this->reflectionService->getClassNameForFile($name);
            $namespaceName = $this->reflectionService->getNamespaceNameForFile($name);

            $class = new ClassModel($className, $namespaceName);
            $classes->addClass($class);

            foreach ($fileNode->warning as $warningNode) {
                $warningAttributes = $warningNode->attributes();

                $line = (string)$warningAttributes['line'];
                //$column = (string)$warningAttributes['column'];
                //$source = (string)$warningAttributes['source'];
                //$severity = (string)$warningAttributes['severity'];
                $message = (string)$warningNode;

                $sourceLines = $this->reflectionService->getSourceLines($name);
                $source = new SourceModel($sourceLines, $line, $line + 1, 5);

                $smell = new SmellModel('code_style', 'CodeStyleWarning', $message, $source, 1);
                $class->addSmell($smell);
            }

            foreach ($fileNode->error as $errorNode) {
                $errorAttributes = $errorNode->attributes();

                $line = (string)$errorAttributes['line'];
                //$column = (string)$errorAttributes['column'];
                //$source = (string)$errorAttributes['source'];
                //$severity = (string)$errorAttributes['severity'];
                $message = (string)$errorNode;

                $sourceLines = $this->reflectionService->getSourceLines($name);
                $source = new SourceModel($sourceLines, $line, $line + 1, 5);

                $smell = new SmellModel('CodeStyle', 'CodeStyleError', $message, $source, 1);
                $class->addSmell($smell);
            }
        }

        return $classes;
    }
}
