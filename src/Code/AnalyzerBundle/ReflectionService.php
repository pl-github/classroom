<?php

namespace Code\AnalyzerBundle;

use TokenReflection\Broker;

class ReflectionService
{
    /**
     * @var \TokenReflection\Broker
     */
    private $broker;

    /**
     * @var array
     */
    private $filenameToClassNameMap = array();

    /**
     * @var array
     */
    private $filenameToNamespaceNameMap = array();

    /**
     * @param Broker $broker
     */
    public function __construct(Broker $broker)
    {
        $this->broker = $broker;
    }

    /**
     * Get class name for file
     *
     * @param string $fileName
     * @return string
     */
    public function getClassNameForFile($fileName)
    {
        $hash = sha1($fileName);

        if (empty($this->filenameToClassNameMap[$hash])) {
            $this->filenameToClassNameMap[$hash] = $this->extractClassNameFromFile($fileName);
        }

        return $this->filenameToClassNameMap[$hash];
    }

    /**
     * Get namespace name for file
     *
     * @param string $filename
     * @return string
     */
    public function getNamespaceNameForFile($filename)
    {
        $hash = sha1($filename);

        if (empty($this->filenameToNamespaceNameMap[$hash])) {
            $this->filenameToNamespaceNameMap[$hash] = $this->extractNamespaceNameFromFile($filename);
        }

        return $this->filenameToNamespaceNameMap[$hash];
    }

    /**
     * Return class source
     *
     * @param string $fileName
     * @param string $className
     * @return string
     */
    public function getClassSource($fileName, $className)
    {
        $class = $this->getClass($fileName, $className);

        return $class->getSource();
    }

    /**
     * Return method source
     *
     * @param string $fileName
     * @param string $className
     * @param string $methodName
     * @return string
     */
    public function getMethodSource($fileName, $className, $methodName)
    {
        $method = $this->getMethod($fileName, $className, $methodName);

        return $method->getSource();
    }

    /**
     * Return source extract
     *
     * @param string  $fileName
     * @param integer $beginLine
     * @param integer $endLine
     */
    public function getSourceExtract($fileName, $beginLine, $endLine)
    {
        $file = $this->getFile($fileName);

        $source = $file->getSource();
        $sourceLines = explode(PHP_EOL, $source);
        $extract = array_slice($sourceLines, $beginLine - 1, $endLine - $beginLine + 1);

        return implode(PHP_EOL, $extract);
    }

    /**
     * Extract classname from file
     *
     * @param string $fileName
     * @return null|string
     */
    private function extractClassNameFromFile($fileName)
    {
        $namespace = $this->extractNamespaceFromFile($fileName);

        $classes = $namespace->getClasses();

        if (!count($classes)) {
            return null;
        }

        $class = current($classes);
        /* @var $class \TokenReflection\IReflectionClass */

        return $class->getShortName();
    }

    /**
     * Extract namespace from file
     *
     * @param string $fileName
     * @return null|string
     */
    private function extractNamespaceNameFromFile($fileName)
    {
        $namespace = $this->extractNamespaceFromFile($fileName);

        if (!$namespace) {
            return null;
        }

        return $namespace->getName();
    }

    /**
     * Extract namespace from file
     *
     * @param string $fileName
     * @return \TokenReflection\ReflectionFileNamespace
     */
    private function extractNamespaceFromFile($fileName)
    {
        $this->broker->processFile($fileName);

        $file = $this->getFile($fileName);
        $namespaces = $file->getNamespaces();

        if (!count($namespaces)) {
            return null;
        }

        $namespace = current($namespaces);
        /* @var $namespace \TokenReflection\ReflectionFileNamespace */

        return $namespace;
    }

    /**
     * Return reflection method
     *
     * @param string $fileName
     * @param string $className
     * @param string $methodName
     * @return \TokenReflection\ReflectionMethod
     */
    private function getMethod($fileName, $className, $methodName)
    {
        $class = $this->getClass($fileName, $className);

        return $class->getMethod($methodName);
    }

    /**
     * Return reflection class
     *
     * @param string $fileName
     * @param string $className
     * @return \TokenReflection\ReflectionClass
     */
    private function getClass($fileName, $className)
    {
        $this->broker->processFile($fileName);

        $file = $this->broker->getClass($className);

        return $file;
    }

    /**
     * Return reflection file
     *
     * @param string $fileName
     * @return \TokenReflection\ReflectionFile
     */
    private function getFile($fileName)
    {
        $this->broker->processFile($fileName);

        $file = $this->broker->getFile($fileName);

        return $file;
    }
}
