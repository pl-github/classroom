<?php

namespace Code\PhpAnalyzerBundle\Phpmd;

use Code\AnalyzerBundle\Analyzer\Collector\CollectorInterface;
use Code\AnalyzerBundle\ProcessExecutor;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\Process\ProcessBuilder;
use Symfony\Component\Process\Process;

class PhpmdCollector implements CollectorInterface
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
    private $phpmdExecutable;

    /**
     * @param ProcessExecutor $processExecutor
     * @param LoggerInterface $logger
     * @param string          $phpmdExecutable
     */
    public function __construct(ProcessExecutor $processExecutor, LoggerInterface $logger, $phpmdExecutable)
    {
        $this->processExecutor = $processExecutor;
        $this->logger = $logger;
        $this->phpmdExecutable = $phpmdExecutable;
    }

    /**
     * @inheritDoc
     */
    public function collect($sourceDirectory, $workDirectory)
    {
        $phpmdFilename = $workDirectory . '/phpmd.xml';
        #return $phpmdFilename;

        if (file_exists($phpmdFilename) && !unlink($phpmdFilename)) {
            throw new \Exception('Can\'t unlink ' . $phpmdFilename);
        }

        $processBuilder = new ProcessBuilder();
        $processBuilder->add($this->phpmdExecutable)
            ->add($sourceDirectory)
            ->add('xml')
            ->add('codesize,unusedcode,naming')
            ->add('--suffixes')->add('php')
            ->add('--reportfile')->add($phpmdFilename);

        $process = $processBuilder->getProcess();

        $this->logger->debug($process->getCommandLine());

        $this->processExecutor->execute($process, 2);

        return $phpmdFilename;
    }
}
