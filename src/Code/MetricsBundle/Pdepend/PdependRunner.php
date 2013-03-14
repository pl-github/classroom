<?php

namespace Code\MetricsBundle\Pdepend;

use Code\AnalyzerBundle\Analyzer\Runner\RunnerInterface;
use Code\AnalyzerBundle\ProcessExecutor;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\Process\ProcessBuilder;
use Symfony\Component\Process\Process;

class PdependRunner implements RunnerInterface
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
    private $pdependExecutable;

    /**
     * @param ProcessExecutor $processExecutor
     * @param LoggerInterface $logger
     * @param string          $pdependExecutable
     */
    public function __construct(ProcessExecutor $processExecutor, LoggerInterface $logger, $pdependExecutable)
    {
        $this->processExecutor = $processExecutor;
        $this->logger = $logger;
        $this->pdependExecutable = $pdependExecutable;
    }

    /**
     * @inheritDoc
     */
    public function run($sourceDirectory, $workDirectory)
    {
        $pdependFilename = $this->ensureDirectoryWritable($workDirectory) . '/pdepend.xml';

        if (file_exists($pdependFilename) && !unlink($pdependFilename)) {
            throw new \Exception('Can\'t unlink ' . $pdependFilename);
        }

        $processBuilder = new ProcessBuilder();
        $processBuilder->add($this->pdependExecutable)
            ->add('--summary-xml=' . $pdependFilename)
            ->add($sourceDirectory);

        $process = $processBuilder->getProcess();

        $this->logger->debug($process->getCommandLine());

        $this->processExecutor->execute($process, 1);

        return $pdependFilename;
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
