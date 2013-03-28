<?php

namespace Classroom\UserBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="classroom_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="github_id", nullable = true)
     */
    protected $githubId;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="github_access_token", nullable = true)
     */
    protected $githubAccessToken;

    public function __construct()
    {
        parent::__construct();
    }

    public function getGithubId()
    {
        return $this->githubId;
    }

    public function setGithubId($githubId)
    {
        $this->githubId = $githubId;

        return $this;
    }

    public function getGithubAccessToken()
    {
        return $this->githubId;
    }

    public function setGithubAccessToken($githubAccessToken)
    {
        $this->githubAccessToken = $githubAccessToken;

        return $this;
    }
}
