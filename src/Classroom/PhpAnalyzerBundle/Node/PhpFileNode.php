<?php

namespace Classroom\PhpAnalyzerBundle\Node;

use Classroom\AnalyzerBundle\Result\Metric\Measurable;
use Classroom\AnalyzerBundle\Result\Metric\MeasurableTrait;
use Classroom\AnalyzerBundle\Result\Node\NodeInterface;
use Classroom\AnalyzerBundle\Result\Node\NodeTrait;

class PhpFileNode implements NodeInterface, Measurable
{
    use NodeTrait;
    use MeasurableTrait;

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->setName($name);
        $this->setHash($this->createHash($name));
    }

    /**
     * @param string $name
     * @return string
     */
    private function createHash($name)
    {
        $hash = $this->replaceBackslashesWithSlashes($name);
        $hash = $this->uppercaseWindowsDriveLetter($hash);

        return $hash;
    }

    /**
     * @param string $name
     * @return string
     */
    private function replaceBackslashesWithSlashes($name)
    {
        return str_replace('\\', '/', $name);
    }

    /**
     * @param string $hash
     * @return string
     */
    private function uppercaseWindowsDriveLetter($hash)
    {
        if ($this->isWindowsPath($hash)) {
            $hash = ucfirst($hash);
        }

        return $hash;
    }

    /**
     * @param string $hash
     * @return bool
     */
    private function isWindowsPath($hash)
    {
        return isset($hash{1}) && $hash{1} === ':';
    }
}
