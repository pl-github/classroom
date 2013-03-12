<?php

namespace Code\RepositoryBundle;

class RepositoryConfig
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $url;

    /**
     * @param string $type
     * @param string $url
     */
    public function __construct($type, $url)
    {
        $this->type = $type;
        $this->url = $url;
    }

    /**
     * Return url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Return type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Return type
     *
     * @return string
     */
    public function getLibDir()
    {
        return $this->libDir;
    }
}
