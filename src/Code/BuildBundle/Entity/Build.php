<?php

namespace Code\BuildBundle\Entity;

use Code\AnalyzerBundle\Model\ClassesModel;
use Code\ProjectBundle\Project;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name = "code_build", indexes = {
 *     @ORM\Index(columns = {"version"}),
 * })
 */
class Build
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy = "AUTO")
     * @ORM\Column(type = "bigint", options = {"unsigned": true})
     * @var integer
     */
    private $id;

    /**
     * @var Project
     * @ORM\ManyToOne(targetEntity="Code\ProjectBundle\Entity\Project")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="project_id", referencedColumnName="id", nullable=false)
     * })
     */
    protected $project;

    /**
     * @var mixed
     * @ORM\Column(type = "string")
     */
    protected $version;

    /**
     * @var float
     * @ORM\Column(type = "float")
     */
    protected $gpa;

    /**
     * @var integer
     * @ORM\Column(type = "bigint")
     */
    protected $runTime;

    /**
     * @var \DateTime
     * @ORM\Column(type = "date")
     */
    protected $createdAt;

    /**
     * Return ID
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
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
     * Set project
     *
     * @param Project $project
     * @return $this
     */
    public function setProject(Project $project)
    {
        $this->project = $project;

        return $this;
    }

    /**
     * Return version
     *
     * @return mixed
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set version
     *
     * @param mixed $version
     * @return $this
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Return GPA
     *
     * @return float
     */
    public function getGpa()
    {
        return $this->version;
    }

    /**
     * Set GPA
     *
     * @param float $gpa
     * @return $this
     */
    public function setGpa($gpa)
    {
        $this->gpa = $gpa;

        return $this;
    }

    /**
     * Return run time
     *
     * @return integer
     */
    public function getRunTime()
    {
        return $this->runTime;
    }

    /**
     * Set run time
     *
     * @param integer $runTime
     * @return $this
     */
    public function setRunTime($runTime)
    {
        $this->runTime = $runTime;

        return $this;
    }

    /**
     * Return created at
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set created at
     *
     * @param \DateTime $createdAt
     * @return $this
     */
    public function setCreateAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
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
