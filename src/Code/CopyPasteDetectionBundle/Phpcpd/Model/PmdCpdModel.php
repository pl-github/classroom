<?php

namespace Code\CopyPasteDetectionBundle\Phpcpd\Model;

use Code\AnalyzerBundle\Analyzer\Model\ModelInterface;

class PmdCpdModel implements ModelInterface
{
    /**
     * @var array
     */
    private $duplications = array();

    /**
     * @param array $duplications
     */
    public function __construct(array $duplications = array())
    {
        $this->duplications = $duplications;
    }

    /**
     * Add duplication
     *
     * @param DuplicationModel $duplication
     * @return $this
     */
    public function addDuplication(DuplicationModel $duplication)
    {
        $this->duplications[] = $duplication;

        return $this;
    }

    /**
     * Return duplications
     *
     * @return array
     */
    public function getDuplications()
    {
        return $this->duplications;
    }
}
