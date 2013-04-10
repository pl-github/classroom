<?php

namespace Classroom\AnalyzerBundle\Result\Reference;

trait ReferencableTrait
{
    /**
     * @var string
     */
    private $hash;

    /**
     * Return hash
     *
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * Set hash
     *
     * @param string $hash
     * @return $this
     */
    public function setHash($hash)
    {
        $this->hash = $hash;

        return $this;
    }
}
