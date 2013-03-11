<?php

namespace Code\AnalyzerBundle\Model;

class SmellModel
{
    /**
     * @var string
     */
    private $origin;

    /**
     * @var string
     */
    private $text;

    /**
     * @var string
     */
    private $codeFragment;

    /**
     * @var integer
     */
    private $score;

    /**
     * @param string  $origin
     * @param string  $text
     * @param string  $codeFragment
     * @param integer $score
     */
    public function __construct($origin, $text, $codeFragment = '', $score = null)
    {
        $this->origin = $origin;
        $this->text = $text;
        $this->codeFragment = $codeFragment;
        $this->score = $score;
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

    /*+
     * Return origin
     *
     * @return string
     */
    public function getOrigin()
    {
        return $this->origin;
    }

    /*+
     * Return text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Return code fragment
     *
     * @return string
     */
    public function getCodeFragment()
    {
        return $this->codeFragment;
    }

    /**
     * Set score
     *
     * @param integer $score
     * @return $this
     */
    public function setScore($score)
    {
        $this->score = $score;

        return $this;
    }

    /*+
     * Return score
     *
     * @return integer
     */
    public function getScore()
    {
        return $this->score;
    }
}
