<?php

namespace Code\PhpAnalyzerBundle\Pdepend;

use Code\AnalyzerBundle\Log\Log;
use Code\AnalyzerBundle\ProcessExecutor;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\Process\ProcessBuilder;
use Symfony\Component\Process\Process;

class PdependCollector
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
    public function collect(Log $log, $sourceDirectory, $workDirectory)
    {
        $pdependFilename = $workDirectory . '/pdepend.xml';
        #return $pdependFilename;

        if (file_exists($pdependFilename) && !unlink($pdependFilename)) {
            throw new \Exception('Can\'t unlink ' . $pdependFilename);
        }

        $processBuilder = new ProcessBuilder();
        $processBuilder->add($this->pdependExecutable)
            ->add('--summary-xml=' . $pdependFilename)
            ->add($sourceDirectory);

        $process = $processBuilder->getProcess();

        $log->addCommand($process->getCommandLine());
        $this->logger->debug($process->getCommandLine());

        $this->processExecutor->execute($process, 1);

        $log->addOutput($process->getOutput());
        $log->addError($process->getErrorOutput());

        return $pdependFilename;
    }
}
