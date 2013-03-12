<?php

namespace Code\MessDetectionBundle\Phpmd\Model;

class ViolationModel
{
    /**
     * @var integer
     */
    private $beginLine;

    /**
     * @var integer
     */
    private $endLine;

    /**
     * @var string
     */
    private $rule;

    /**
     * @var string
     */
    private $ruleset;

    /**
     * @var string
     */
    private $externalInfoUrl;

    /**
     * @var integer
     */
    private $priority;

    /**
     * @var string
     */
    private $text;

    /**
     * @param integer $beginLine
     * @param integer $endLine
     * @param string  $rule
     * @param string  $ruleset
     * @param string  $externalInfoUrl
     * @param integer $priority
     * @param string  $text
     */
    public function __construct($beginLine, $endLine, $rule, $ruleset, $externalInfoUrl, $priority, $text)
    {
        $this->beginLine = $beginLine;
        $this->endLine = $endLine;
        $this->rule = $rule;
        $this->ruleset = $ruleset;
        $this->externalInfoUrl = $externalInfoUrl;
        $this->priority = $priority;
        $this->text = $text;
    }

    /**
     * Return begin line
     *
     * @return integer
     */
    public function getBeginLine()
    {
        return $this->beginLine;
    }

    /**
     * Return end line
     *
     * @return integer
     */
    public function getEndLine()
    {
        return $this->endLine;
    }

    /**
     * Return rule
     *
     * @return string
     */
    public function getRule()
    {
        return $this->rule;
    }

    /**
     * Return ruleset
     *
     * @return string
     */
    public function getRuleset()
    {
        return $this->ruleset;
    }

    /**
     * Return external info url
     *
     * @return string
     */
    public function getExternalInfoUrl()
    {
        return $this->externalInfoUrl;
    }

    /**
     * Return priority
     *
     * @return integer
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Return text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }
}
