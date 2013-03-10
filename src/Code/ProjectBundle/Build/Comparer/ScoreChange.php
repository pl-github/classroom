<?php

namespace Code\ProjectBundle\Build\Comparer;

use Code\ProjectBundle\Model\ClassModel;

class ScoreChange implements ChangeInterface
{
    /**
     * @var ClassModel
     */
    private $class;

    /**
     * @var integer
     */
    private $fromScore;

    /**
     * @var integer
     */
    private $toScore;

    /**
     * @param ClassModel $class
     * @param integer    $fromScore
     * @param integer    $toScore
     */
    public function __construct(ClassModel $class, $fromScore, $toScore)
    {
        $this->class = $class;
        $this->fromScore = $fromScore;
        $this->toScore = $toScore;
    }

    /**
     * @inheritDoc
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @inheritDoc
     */
    public function getText()
    {
        return 'Score changed from ' . $this->fromScore . ' to ' . $this->toScore;
    }
}