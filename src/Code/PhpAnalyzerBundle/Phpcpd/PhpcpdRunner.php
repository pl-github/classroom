<?php

namespace Code\CopyPasteDetectionBundle\Phpcpd;

use Code\AnalyzerBundle\Analyzer\Runner\RunnerInterface;
use Code\AnalyzerBundle\ProcessExecutor;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\Process\ProcessBuilder;
use Symfony\Component\Process\Process;

class PhpcpdRunner implements RunnerInterface
{
    /**
     * @var ProcessExecutor
     */
    private $processExecutor;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var string
     */
    private $phpcpdExecutable;

    /**
     * @param ProcessExecutor $processExecutor
     * @param LoggerInterface $logger
     * @param string          $phpcpdExecutable
     */
    public function __construct(ProcessExecutor $processExecutor, LoggerInterface $logger, $phpcpdExecutable)
    {
        $this->processExecutor = $processExecutor;
        $this->logger = $logger;
        $this->phpcpdExecutable = $phpcpdExecutable;
    }

    /**
     * @inheritDoc
     */
    public function run($sourceDirectory, $workDirectory)
    {
        $phpcpdFilename = $this->ensureDirectoryWritable($workDirectory) . '/phpcpd.xml';

        if (file_exists($phpcpdFilename) && !unlink($phpcpdFilename)) {
            throw new \Exception('Can\'t unlink ' . $phpcpdFilename);
        }

        $processBuilder = new ProcessBuilder();
        $processBuilder
            ->add($this->phpcpdExecutable)
            ->add('--log-pmd')
            ->add($phpcpdFilename)
            ->add('--quiet')
            ->add($sourceDirectory);

        $process = $processBuilder->getProcess();

        $this->logger->debug($process->getCommandLine());

        $this->processExecutor->execute($process, 1);

        return $phpcpdFilename;
    }

    /**
     * Ensure directory exists and is writable
     *
     * @param string $directory
     * @return string
     * @throws \Exception
     */
    private function ensureDirectoryWritable($directory)
    {
        if (!file_exists($directory) && !mkdir($directory, 0777, true)) {
            throw new \Exception('Can\'t create data dir');
        }

        if (!is_writable($directory)) {
            throw new \Exception('Data dir not writable');
        }

        return $directory;
    }
}
