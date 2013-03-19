<?php

namespace Code\AnalyzerBundle\Model;

class Reference
{
    /**
     * @var string
     */
    private $referenceHash;

    /**
     * @param Referencable $node
     */
    public function __construct(Referencable $referencable)
    {
        $this->referenceHash = $referencable->getHash();
    }

    /*+
     * Return reference hash
     *
     * @return string
     */
    public function getReferenceHash()
    {
        return $this->referenceHash;
    }
}
