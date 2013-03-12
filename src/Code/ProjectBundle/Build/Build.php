<?php

namespace Code\ProjectBundle\Build;

use Code\AnalyzerBundle\Model\ClassesModel;
use Code\ProjectBundle\Project;

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
    public function __construct(Project $project, $version, ClassesModel $classes)
    {
        $this->project = $project;
        $this->version = $version;
        $this->classes = $classes;
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
     * Return version
     *
     * @return mixed string
     */
    public function getVersion()
    {
        return $this->version;
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
        foreach ($this->getClasses()->getClasses() as $class) {
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
    public function getHotspots($length = 8)
    {
        $hotspots = array();
        $scores = array();
        foreach ($this->getClasses()->getClasses() as $class) {
            $scores[] = $class->getScore();
            $hotspots[] = array(
                'name' => $class->getName(),
                'fullQualifiedName' => $class->getFullQualifiedName(),
                'score' => $class->getScore()
            );
        }

        array_multisort($scores, $hotspots);

        $hotspots = array_slice($hotspots, -$length);
        $hotspots = array_reverse($hotspots);

        return $hotspots;
    }
}
