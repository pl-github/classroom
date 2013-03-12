<?php

namespace Code\MessDetectionBundle\Phpmd\Model;

class FileModel
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $violations = array();

    /**
     * @param string $name
     * @param string $file
     * @param array  $metrics
     * @param array  $methods
     */
    public function __construct($name, array $violations = array())
    {
        $this->name = $name;
        $this->violations = $violations;
    }

    /**
     * Return name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Add violation
     *
     * @param ViolationModel $violation
     * @return $this
     */
    public function addViolation(ViolationModel $violation)
    {
        $this->violations[] = $violation;

        return $this;
    }

    /**
     * Return violations
     *
     * @return array
     */
    public function getViolations()
    {
        return $this->violations;
    }
}
