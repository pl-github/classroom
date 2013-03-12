<?php

namespace Code\ProjectBundle\Change;

use Code\AnalyzerBundle\Model\ClassModel;

class ScoreChange implements ChangeInterface
{
    /**
     * @var string
     */
    private $className;

    /**
     * @var integer
     */
    private $fromScore;

    /**
     * @var integer
     */
    private $toScore;

    /**
     * @var \DateTime
     */
    private $date;

    /**
     * @param string  $className
     * @param integer $fromScore
     * @param integer $toScore
     */
    public function __construct($className, $fromScore, $toScore)
    {
        $this->className = $className;
        $this->fromScore = $fromScore;
        $this->toScore = $toScore;

        $this->date = new \DateTime();
    }

    /**
     * @inheritDoc
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * Return from score
     *
     * @return integer
     */
    public function getFromScore()
    {
        return $this->fromScore;
    }

    /**
     * Return to score
     *
     * @return integer
     */
    public function getToScore()
    {
        return $this->toScore;
    }

    /**
     * @inheritDoc
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @inheritDoc
     */
    public function getType()
    {
        return 'score_change';
    }
}
