<?php

namespace Code\AnalyzerBundle\Result\Reference;

class Reference
{
    /**
     * @var string
     */
    private $referenceHash;

    /**
     * @param Referencable $referencable
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
