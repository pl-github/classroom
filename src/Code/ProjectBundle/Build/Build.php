<?php

namespace Code\ProjectBundle\Build;

use Code\ProjectBundle\Project;
use Code\ProjectBundle\Model\ClassesModel;

class Build
{
    /**
     * @var Project
     */
    protected $project;

    /**
     * @var mixed
     */
    protected $version;

    /**
     * @var ClassesModel
     */
    protected $classes;

    /**
     * @param Project $project
     */
    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    /**
     * Return project
     *
     * @return Project
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * Set version
     *
     * @param mixed $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * Return version
     *
     * @return mixed string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set classes
     *
     * @param ClassesModel $classes
     */
    public function setClasses(ClassesModel $classes)
    {
        $this->classes = $classes;
    }

    /**
     * Return classes
     *
     * @return ClassesModel
     */
    public function getClasses()
    {
        return $this->classes;
    }

    /**
     * Return breakdown
     *
     * @return array
     */
    public function getBreakdown()
    {
        $breakdown = array();
        foreach ($this->getClasses()->getClasses() as $class)
        {
            if (!isset($breakdown[$class->getScore()])) {
                $breakdown[$class->getScore()] = 0;
            }
            $breakdown[$class->getScore()]++;
        }

        ksort($breakdown);

        return $breakdown;
    }

    /**
     * Return hotspots
     *
     * @param integer $length
     * @return array
     */
    public function getHotspots($length = 5)
    {
        $map = array();
        foreach ($this->getClasses()->getClasses() as $class)
        {
            $map[$class->getName()] = $class->getScore();
        }

        arsort($map);
        $map = array_slice($map, 0, $length, true);

        return $map;
    }
}