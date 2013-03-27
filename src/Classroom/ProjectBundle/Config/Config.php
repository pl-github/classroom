<?php

namespace Classroom\ProjectBundle\Config;

class Config
{
    /**
     * @var string
     */
    private $language;

    /**
     * @var string
     */
    private $target;

    /**
     * @var array
     */
    private $analyzers = array();

    /**
     * @var array
     */
    private $notifications = array();

    /**
     * @param string $analyzerName
     * @param mixed $analyzerConfig
     */
    public function addExtraAnalyzer($analyzerName, $analyzerConfig)
    {
        $this->analyzers[$analyzerName] = $analyzerConfig;
    }

    /**
     * @return array
     */
    public function getAnalyzers()
    {
        return $this->analyzers;
    }

    /**
     * @param string $language
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param string $target
     */
    public function setTarget($target)
    {
        $this->target = $target;
    }

    /**
     * @return string
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * @param string $notificationName
     * @param mixed $notificationConfig
     */
    public function addNotification($notificationName, $notificationConfig)
    {
        switch ($notificationName) {
            case 'hipchat':
                $this->notifications[$notificationName] = $notificationConfig;
                break;
        }
    }

    /**
     * @return array
     */
    public function getNotifications()
    {
        return $this->notifications;
    }
}
