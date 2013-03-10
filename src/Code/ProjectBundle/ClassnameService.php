<?php

namespace Code\ProjectBundle;

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
