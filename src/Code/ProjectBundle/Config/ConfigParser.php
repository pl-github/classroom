<?php

namespace Code\ProjectBundle\Config;

use Symfony\Component\Yaml\Yaml;

class ConfigParser
{
    /**
     * @param string $filename
     * @return Config
     */
    public function parse($filename)
    {
        $yaml = new Yaml();
        $data = $yaml->parse($filename);
        $config = new Config();

        if (!empty($data['language'])) {
            $config->setLanguage($data['language']);
        }

        if (!empty($data['target'])) {
            $config->setTarget($data['target']);
        }

        if (!empty($data['extra'])) {
            foreach ($data['extra'] as $analyzerName => $analyzerConfig) {
                $config->addExtraAnalyzer($analyzerName, $analyzerConfig);
            }
        }

        if (!empty($data['notifications'])) {
            foreach ($data['notifications'] as $notificationName => $notificationConfig) {
                $config->addNotification($notificationName, $notificationConfig);
            }
        }


        return $config;
    }
}