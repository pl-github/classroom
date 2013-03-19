<?php

namespace Code\AnalyzerBundle\Model;

use Code\AnalyzerBundle\Node\NodeInterface;
use Code\AnalyzerBundle\Smell\SmellInterface;
use Code\AnalyzerBundle\Source\SourceInterface;

class ResultModel
{
    /**
     * @var NodeInterface[]
     */
    private $nodes = array();

    /**
     * @var Smell[]
     */
    private $smells = array();

    /**
     * @var Source[]
     */
    private $sources = array();

    /**
     * @var array
     */
    private $references = array();

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
     * @param string $type
     * @param NodeInterface|Referencable|string $hash
     * @return array
     */
    public function getIncoming($type, $hash)
    {
        $hash = $this->extractHash($hash);

        return $this->references['incoming'][$type][$hash];
    }

    /**
     * Return outgoing references
     *
     * @param string $type
     * @param NodeInterface|Referencable|string $hash
     * @return Reference
     */
    public function getOutgoing($type, $hash)
    {
        $hash = $this->extractHash($hash);

        return $this->references['outgoing'][$type][$hash];
    }

    /**
     * Return references
     *
     * @return array
     */
    public function getReferences()
    {
        return $this->references;
    }

    /**
     * Add node
     *
     * @param NodeInterface                    $node
     * @param Referencable|Referencable|string $referenceTo
     * @return $this
     * @throws \Exception
     */
    public function addNode(NodeInterface $node, $referenceTo = null)
    {
        $nodeHash = $node->getHash();

        $this->nodes[$nodeHash] = $node;

        if ($referenceTo) {
            $referenceHash = $this->extractHash($referenceTo);

            $this->references['incoming']['node'][$referenceHash][] = new Reference($node);
            $this->references['outgoing']['node'][$nodeHash] = new Reference($referenceTo);
        }

        return $this;
    }

    /**
     * Return node
     *
     * @param Referencable|Referencable|string $hash
     * @return NodeInterface|null
     */
    public function getNode($hash)
    {
        $hash = $this->extractHash($hash);

        if (!$this->hasNode($hash)) {
            return null;
        }

        return $this->nodes[$hash];
    }

    /**
     * Is this node set?
     *
     * @param Referencable|Referencable|string $hash
     * @return boolean
     */
    public function hasNode($hash)
    {
        $hash = $this->extractHash($hash);

        return !empty($this->nodes[$hash]);
    }

    /**
     * Add smell
     *
     * @param SmellInterface                   $smell
     * @param Referencable|Referencable|string $referenceTo
     * @return $this
     */
    public function addSmell(SmellInterface $smell, $referenceTo = null)
    {
        $smellHash = $smell->getHash();

        $this->smells[$smellHash] = $smell;

        if ($referenceTo) {
            $referenceHash = $this->extractHash($referenceTo);

            $this->references['incoming']['smell'][$referenceHash][] = new Reference($smell);
            $this->references['outgoing']['smell'][$smellHash] = new Reference($referenceTo);
        }
    }

    /**
     * Return smell
     *
     * @param Referencable|Referencable|string $hash
     * @return SmellInterface|null
     */
    public function getSmell($hash)
    {
        $hash = $this->extractHash($hash);

        if (!$this->hasSmell($hash)) {
            return null;
        }

        return $this->smells[$hash];
    }

    /**
     * Is this smell set?
     *
     * @param Referencable|Referencable|string $hash
     * @return boolean
     */
    public function hasSmell($hash)
    {
        $hash = $this->extractHash($hash);

        return !empty($this->smells[$hash]);
    }

    /**
     * Return smells
     *
     * @return SmellInterface[]
     */
    public function getSmells()
    {
        return $this->smells;
    }

    /**
     * Add source
     *
     * @param SourceInterface                  $source
     * @param Referencable|Referencable|string $referenceTo
     * @return $this
     */
    public function addSource(SourceInterface $source, $referenceTo = null)
    {
        $sourceHash = $source->getHash();

        $this->sources[$sourceHash] = $source;

        if ($referenceTo) {
            $referenceHash = $this->extractHash($referenceTo);

            $this->references['incoming']['source'][$referenceHash] = new Reference($source);
            $this->references['outgoing']['source'][$sourceHash] = new Reference($referenceTo);
        }

        return $this;
    }

    /**
     * Return source
     *
     * @param Referencable|Referencable|string $hash
     * @return SourceInterface|null
     */
    public function getSource($hash)
    {
        $hash = $this->extractHash($hash);

        if (!$this->hasSource($hash)) {
            return null;
        }

        return $this->sources[$hash];
    }

    /**
     * Is this source set?
     *
     * @param Referencable|Referencable|string $hash
     * @return boolean
     */
    public function hasSource($hash)
    {
        $hash = $this->extractHash($hash);

        return !empty($this->sources[$hash]);
    }

    /**
     * Return sources
     *
     * @return SourceInterface[]
     */
    public function getSources()
    {
        return $this->sources;
    }

    /**
     * Add single reference
     *
     * @param string $dir
     * @param string $type
     * @param string $hash
     * @param string $referenceHash
     */
    public function addSingleReference($dir, $type, $hash, $referenceHash)
    {
        $this->references[$dir][$type][$hash] = new Reference($referenceHash);
    }

    /**
     * Add multi reference
     *
     * @param string $dir
     * @param string $type
     * @param string $hash
     * @param string $referenceHash
     */
    public function addMultiReference($dir, $type, $hash, $referenceHash)
    {
        $this->references[$dir][$type][$hash][] = new Reference($referenceHash);
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

    /**
     * Extract and return hash
     *
     * @param Reference|Referencable|string $referenceTo
     * @return string
     * @throws \Exception
     */
    private function extractHash($referenceTo)
    {
        if ($referenceTo instanceof Reference) {
            $referenceHash = $referenceTo->getReferenceHash();
        } elseif ($referenceTo instanceof Referencable) {
            $referenceHash = $referenceTo->getHash();
        } elseif (is_string($referenceTo)) {
            $referenceHash = $referenceTo;
        } else {
            $msg = 'Neither a node reference nor a node interface nor a string (' . gettype($referenceTo) . ')';
            throw new \Exception($msg);
        }

        return $referenceHash;
    }

    /**
     * Extract and return hash
     *
     * @param NodeInterface|string $node
     * @return string
     * @throws \Exception
     */
    private function extractName($node)
    {
        if ($node instanceof NodeInterface) {
            $nodeName = $node->getName();
        } elseif (is_string($node)) {
            $nodeName = $node;
        } else {
            $msg = 'Neither a node interface nor a string (' . gettype($node) . ')';
            throw new \Exception($msg);
        }

        return $nodeName;
    }
}
