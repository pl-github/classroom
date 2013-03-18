<?php

namespace Code\AnalyzerBundle\Model;

use Code\AnalyzerBundle\Node\NodeInterface;
use Code\AnalyzerBundle\Node\NodeReference;

class ResultModel
{
    /**
     * @var array
     */
    private $nodes = array();

    /**
     * @var array
     */
    private $smells = array();

    /**
     * @var array
     */
    private $incomingReferences = array();

    /**
     * @var array
     */
    private $outgoingReferences = array();

    /**
     * @param array $classes
     */
    public function __construct(array $nodes = array(), array $smells = array())
    {
        foreach ($nodes as $node) {
            $this->addNode($node);
        }

        foreach ($smells as $smell) {
            $this->addSmell($smell);
        }
    }

    /**
     * Return id
     *
     * @return string
     */
    public function getId()
    {
        return spl_object_hash($this);
    }

    /**
     * @inheritDoc
     */
    public function getNodes()
    {
        return $this->nodes;
    }

    /**
     * Return incoming references
     *
     * @param string|NodeInterface|NodeReference $fullQualifiedName
     * @return array
     */
    public function getIncomingReferences($fullQualifiedName)
    {
        if ($fullQualifiedName instanceof NodeInterface) {
            $fullQualifiedName = $fullQualifiedName->getFullQualifiedName();
        } elseif ($fullQualifiedName instanceof NodeReference) {
            $fullQualifiedName = $fullQualifiedName->getReferenceName();
        }

        return $this->incomingReferences[$fullQualifiedName];
    }

    /**
     * Return outgoing references
     *
     * @param string|NodeInterface|NodeReference $fullQualifiedName
     * @return NodeReference
     */
    public function getOutgoingReference($fullQualifiedName)
    {
        if ($fullQualifiedName instanceof NodeInterface) {
            $fullQualifiedName = $fullQualifiedName->getFullQualifiedName();
        } elseif ($fullQualifiedName instanceof NodeReference) {
            $fullQualifiedName = $fullQualifiedName->getReferenceName();
        }

        return $this->outgoingReferences[$fullQualifiedName];
    }

    /**
     * Add node
     *
     * @param NodeInterface $node
     * @return $this
     */
    public function addNode(NodeInterface $node)
    {
        $nodeId = $node->getFullQualifiedName();

        $this->nodes[$nodeId] = $node;

        $parentNodeReference = $node->getParentNodeReference();
        if ($parentNodeReference) {
            $parentNodeId = $parentNodeReference->getReferenceName();
            $this->incomingReferences[$parentNodeId][] = new NodeReference($node);
            $this->outgoingReferences[$nodeId] = $parentNodeReference;
        }

        return $this;
    }

    /**
     * Return class by full qualified name
     *
     * @param string|NodeInterface|NodeReference $fullQualifiedName
     * @return NodeInterface
     */
    public function getNode($fullQualifiedName)
    {
        if ($fullQualifiedName instanceof NodeInterface) {
            $fullQualifiedName = $fullQualifiedName->getFullQualifiedName();
        } elseif ($fullQualifiedName instanceof NodeReference) {
            $fullQualifiedName = $fullQualifiedName->getReferenceName();
        }

        if (!$this->hasNode($fullQualifiedName)) {
            return null;
        }

        return $this->nodes[$fullQualifiedName];
    }

    /**
     * Is this node set?
     *
     * @param string|NodeInterface|NodeReference $fullQualifiedName
     * @return boolean
     */
    public function hasNode($fullQualifiedName)
    {
        if ($fullQualifiedName instanceof NodeInterface) {
            $fullQualifiedName = $fullQualifiedName->getFullQualifiedName();
        } elseif ($fullQualifiedName instanceof NodeReference) {
            $fullQualifiedName = $fullQualifiedName->getReferenceName();
        }

        return !empty($this->nodes[$fullQualifiedName]);
    }

    /**
     * Add smell
     *
     * @param SmellModel $smell
     * @return $this
     */
    public function addSmell(SmellModel $smell)
    {
        $this->smells[] = $smell;
    }

    /**
     * Return smells
     *
     * @return array
     */
    public function getSmells()
    {
        return $this->smells;
    }

    /**
     * Return score
     *
     * @return integer
     */
    public function getScore()
    {
        if (!$this->hasSmells()) {
            return 0;
        }

        $score = 0;
        foreach ($this->smells as $smell) {
            $score += $smell->getScore();
        }

        if (!$this->hasMetric('linesOfCode')) {
            return 0;
        }

        #$linesOfCode = $this->getMetric('linesOfCode')->getValue();
        #if ($linesOfCode) {
        #    $score /= $linesOfCode;
        #}

        return $score;
    }
}
