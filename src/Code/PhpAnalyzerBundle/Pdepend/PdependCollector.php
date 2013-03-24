<?php

namespace Code\PhpAnalyzerBundle\Pdepend;

use Code\AnalyzerBundle\Analyzer\Collector\CollectorInterface;
use Code\AnalyzerBundle\ProcessExecutor;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\Process\ProcessBuilder;
use Symfony\Component\Process\Process;

class PdependCollector implements CollectorInterface
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
    public function collect($sourceDirectory, $workDirectory)
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

        $this->logger->debug($process->getCommandLine());

        $this->processExecutor->execute($process, 1);

        return $pdependFilename;
    }
}
