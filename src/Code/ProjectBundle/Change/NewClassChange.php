<?php

namespace Code\ProjectBundle\Change;

use Code\AnalyzerBundle\Model\ClassModel;

class NewClassChange implements ChangeInterface
{
    /**
     * @var string
     */
    private $className;

    /**
     * @var integer
     */
    private $score;

    /**
     * @var \DateTime
     */
    private $date;

    /**
     * @param string $className
     * @param string $score
     */
    public function __construct($className, $score)
    {
        $this->className = $className;
        $this->score = $score;

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
     * @inheritDoc
     */
    public function getScore()
    {
        return $this->score;
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
        return 'new_class';
    }
}
