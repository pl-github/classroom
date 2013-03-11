<?php

namespace Code\AnalyzerBundle;

use TokenReflection\Broker;

class ClassnameService
{
    /**
     * @var \TokenReflection\Broker
     */
    private $broker;

    /**
     * @var array
     */
    private $filenameToClassnameMap = array();

    /**
     * @param Broker $broker
     */
    public function __construct(Broker $broker)
    {
        $this->broker = $broker;
    }

    /**
     * Get classname for file
     *
     * @param string $filename
     * @return string
     */
    public function getClassnameForFile($filename)
    {
        $hash = sha1($filename);

        if (empty($this->filenameToClassnameMap[$hash])) {
            $this->filenameToClassnameMap[$hash] = $this->extractClassnameFromFile($filename);
        }

        return $this->filenameToClassnameMap[$hash];
    }

    /**
     * Return class source
     *
     * @param string $filename
     * @param string $className
     * @return string
     */
    public function getClassSource($filename, $className)
    {
        $this->broker->processFile($filename);
        $class = $this->broker->getClass($className);
        return $class->getSource();
    }

    /**
     * Return method source
     *
     * @param string $filename
     * @param string $className
     * @param string $methodName
     * @return string
     */
    public function getMethodSource($filename, $className, $methodName)
    {
        $this->broker->processFile($filename);
        $class = $this->broker->getClass($className);
        $method = $class->getMethod($methodName);
        return $method->getSource();
    }

    /**
     * Extract classname from file
     *
     * @param string $filename
     * @return null|string
     */
    private function extractClassnameFromFile($filename)
    {
        $this->broker->processFile($filename);

        $classes = $this->broker->getClasses();

        if (!count($classes))
        {
            return null;
        }

        $class = current($classes);
        /* @var $class \TokenReflection\IReflectionClass */

        return $class->getName();
    }
}
