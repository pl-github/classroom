<?php

namespace Code\AnalyzerBundle\Model;

class NodeReference
{
    /**
     * @var string
     */
    private $referenceName;

    /**
     * @param NodeInterface $node
     */
    public function __construct(NodeInterface $node)
    {
        $this->referenceName = $node->getFullQualifiedName();
    }

    /*+
     * Return reference name
     *
     * @return string
     */
    public function getReferenceName()
    {
        return $this->referenceName;
    }
}
