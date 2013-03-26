<?php

namespace Code\PhpAnalyzerBundle\Phpcpd;

use Code\AnalyzerBundle\Log\Log;
use Code\AnalyzerBundle\ProcessExecutor;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\Process\ProcessBuilder;
use Symfony\Component\Process\Process;

class PhpcpdCollector
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
    public function collect(Log $log, $sourceDirectory, $workDirectory)
    {
        $phpcpdFilename = $workDirectory . '/phpcpd.xml';
        #return $phpcpdFilename;

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

        $log->addCommand($process->getCommandLine());
        $this->logger->debug($process->getCommandLine());

        $this->processExecutor->execute($process, 1);

        $log->addOutput($process->getOutput());
        $log->addError($process->getErrorOutput());

        return $phpcpdFilename;
    }
}
