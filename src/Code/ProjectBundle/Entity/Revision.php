<?php

namespace Code\ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name = "code_project_revision", indexes = {
 *     @ORM\Index(columns = {"revision"}),
 * })
 */
class Revision
{
    const STATUS_NEW = 0;
    const STATUS_BUILT = 1;

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
     * @ORM\Column(type = "string", nullable = true)
     */
    protected $revision;

    /**
     * @var string
     * @ORM\Column(type = "string", nullable = true)
     */
    protected $resultFilename;

    /**
     * @var integer
     * @ORM\Column(type = "integer")
     */
    protected $status;

    /**
     * @var float
     * @ORM\Column(type = "float", nullable = true)
     */
    protected $gpa;

    /**
     * @var integer
     * @ORM\Column(type = "bigint", nullable = true)
     */
    protected $runTime;

    /**
     * @var \DateTime
     * @ORM\Column(type = "date")
     */
    protected $createdAt;

    /**
     * @var \DateTime
     * @ORM\Column(type = "date", nullable = true)
     */
    protected $builtAt;

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
     * Return revision
     *
     * @return mixed
     */
    public function getRevision()
    {
        return $this->revision;
    }

    /**
     * Set version
     *
     * @param mixed $revision
     * @return $this
     */
    public function setRevision($revision)
    {
        $this->revision = $revision;

        return $this;
    }

    /**
     * Return result filename
     *
     * @return string
     */
    public function getResultFilename()
    {
        return $this->resultFilename;
    }

    /**
     * Set result filename
     *
     * @param string $resultFilename
     * @return $this
     */
    public function setResultFilename($resultFilename)
    {
        $this->resultFilename = $resultFilename;

        return $this;
    }

    /**
     * Return status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;

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
     * Return built at
     *
     * @return \DateTime
     */
    public function getBuiltAt()
    {
        return $this->builtAt;
    }

    /**
     * Set built at
     *
     * @param \DateTime $builtAt
     * @return $this
     */
    public function setBuiltAt(\DateTime $builtAt)
    {
        $this->builtAt = $builtAt;

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
