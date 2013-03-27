<?php

namespace Classroom\AnalyzerBundle\Result\Reference;

interface Referencable
{
    /*+
     * Return hash
     *
     * @return string
     */
    public function getHash();

    /*+
     * Set hash
     *
     * @param string $hash
     * @return $this
     */
    public function setHash($hash);
}
