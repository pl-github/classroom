<?php

namespace Classroom\PhpAnalyzerBundle\PreProcessor;

use Classroom\AnalyzerBundle\Log\Log;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpKernel\Log\LoggerInterface;

class BaseCollector
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function collect(Log $log, $sourceDirectory, $workDirectory)
    {
        $finder = new Finder();
        $finder->name('*.php');

        $files = array();
        foreach ($finder->in($sourceDirectory) as $file) {
            /* @var $file SplFileInfo */

            $files[] = $file->getPathname();
        }

        $this->logger->debug('Collected ' . count($files) . ' files.');

        return $files;
    }
}
