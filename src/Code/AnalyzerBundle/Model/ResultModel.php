<?php

namespace Code\AnalyzerBundle\Model;

use Code\AnalyzerBundle\Node\NodeInterface;
use Code\AnalyzerBundle\Smell\SmellInterface;
use Code\AnalyzerBundle\Source\SourceInterface;

class ResultModel
{
    const REFERENCE_TYPE_NODE = 'node';
    const REFERENCE_TYPE_SMELL = 'smell';
    const REFERENCE_TYPE_SOURCE = 'source';

    const REFERENCE_DIR_NODE_CHILDREN = 'children';
    const REFERENCE_DIR_NODE_PARENT = 'parent';
    const REFERENCE_DIR_SMELL_TO_NODE = 'smellToNode';
    const REFERENCE_DIR_NODE_TO_SMELLS = 'nodeToSmells';
    const REFERENCE_DIR_SOURCE_TO_NODE = 'sourceToNode';
    const REFERENCE_DIR_NODE_TO_SOURCE = 'nodeToSource';

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
     * @var array
     */
    private $artifacts = array();

    /**
     * @inheritDoc
     */
    public function getNodes()
    {
        return $this->nodes;
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

            $this->addMultiReference(
                self::REFERENCE_TYPE_NODE,
                self::REFERENCE_DIR_NODE_CHILDREN,
                $referenceHash,
                $node
            );
            $this->addSingleReference(
                self::REFERENCE_TYPE_NODE,
                self::REFERENCE_DIR_NODE_PARENT,
                $nodeHash,
                $referenceTo
            );
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
     * Remove node
     *
     * @param NodeInterface $node
     * @return $this
     */
    public function removeNode(NodeInterface $node)
    {
        $nodeHash = $node->getHash();

        unset($this->nodes[$nodeHash]);

        $this->removeReferences($node);

        return $this;
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

            $this->addMultiReference(
                self::REFERENCE_TYPE_SMELL,
                self::REFERENCE_DIR_NODE_TO_SMELLS,
                $referenceHash,
                $smell
            );
            $this->addSingleReference(
                self::REFERENCE_TYPE_SMELL,
                self::REFERENCE_DIR_SMELL_TO_NODE,
                $smellHash,
                $referenceTo
            );
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
     * Remove smell
     *
     * @param Referencable|Referencable|string $hash
     * @return $this
     */
    public function removeSmell($hash)
    {
        $hash = $this->extractHash($hash);

        unset($this->smells[$hash]);

        $this->removeReferences($hash);

        return $this;
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
     * Are smells available?
     *
     * @return boolean
     */
    public function hasSmells()
    {
        return count($this->smells) > 0;
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

            $this->addSingleReference(
                self::REFERENCE_TYPE_SOURCE,
                self::REFERENCE_DIR_NODE_TO_SOURCE,
                $referenceHash,
                $source
            );
            $this->addSingleReference(
                self::REFERENCE_TYPE_SOURCE,
                self::REFERENCE_DIR_SOURCE_TO_NODE,
                $sourceHash,
                $referenceTo
            );
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
     * Remove source
     *
     * @param Referencable|Referencable|string $hash
     * @return $this
     */
    public function removeSource($hash)
    {
        $hash = $this->extractHash($hash);

        unset($this->source[$hash]);

        $this->removeReferences($hash);

        return $this;
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
     * Are sources available?
     *
     * @return boolean
     */
    public function hasSources()
    {
        return count($this->sources) > 0;
    }

    /**
     * Add single reference
     *
     * @param string $type
     * @param string $dir
     * @param string $hash
     * @param string $referenceHash
     * @return $this
     */
    public function addSingleReference($type, $dir, $hash, $referenceHash)
    {
        $this->references[$type][$dir][$hash] = new Reference($referenceHash);

        return $this;
    }

    /**
     * Add multi reference
     *
     * @param string $type
     * @param string $dir
     * @param string $hash
     * @param string $referenceHash
     * @return $this
     */
    public function addMultiReference($type, $dir, $hash, $referenceHash)
    {
        $this->references[$type][$dir][$hash][] = new Reference($referenceHash);

        return $this;
    }

    /**
     * Return incoming references
     *
     * @param string $dir
     * @param string $type
     * @param NodeInterface|Referencable|string $hash
     * @return array
     */
    public function getReference($type, $dir, $hash)
    {
        $hash = $this->extractHash($hash);

        return $this->references[$type][$dir][$hash];
    }

    /**
     * @param string                            $type
     * @param string                            $dir
     * @param NodeInterface|Referencable|string $hash
     * @return boolean
     */
    public function hasReference($type, $dir, $hash)
    {
        $hash = $this->extractHash($hash);

        return !empty($this->references[$type][$dir][$hash]);
    }

    /**
     * Remove alle references to hash
     *
     * @param $removeHash
     */
    public function removeReferences($removeHash)
    {
        $removeHash = $this->extractHash($removeHash);

        foreach ($this->references as $type => $typeReferences) {
            foreach ($typeReferences as $direction => $directionReferences) {
                foreach ($directionReferences as $hash => $referenceHashes) {
                    if ($removeHash === $hash) {
                        unset($this->references[$type][$direction][$hash]);
                        continue;
                    }

                    if (is_array($referenceHashes)) {
                        foreach ($referenceHashes as $referenceHashKey => $referenceHash) {
                            $referenceHashString = $this->extractHash($referenceHash);

                            if ($removeHash === $referenceHashString) {
                                unset($this->references[$type][$direction][$hash][$referenceHashKey]);
                            }
                        }
                    } else {
                        $referenceHashString = $this->extractHash($referenceHashes);

                        if ($removeHash === $referenceHashString) {
                            unset($this->references[$type][$direction][$hash]);
                        }
                    }
                }
            }
        }
    }

    /**
     * Add build artifact
     *
     * @param string $filename
     * @return $this
     */
    public function addArtifact($filename)
    {
        $this->artifacts[] = $filename;

        return $this;
    }

    /**
     * Return artifacts
     *
     * @return array
     */
    public function getArtifacts()
    {
        return $this->artifacts;
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
