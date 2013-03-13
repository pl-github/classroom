<?php

namespace Code\CodeStyleBundle\Phpcs;

use Code\AnalyzerBundle\Analyzer\Runner\RunnerInterface;
use Symfony\Component\Process\ProcessBuilder;
use Symfony\Component\Process\Process;

class PhpcsRunner implements RunnerInterface
{
    /**
     * @var string
     */
    private $phpcsExecutable;

    /**
     * @param string $phpcsExecutable
     */
    public function __construct($phpcsExecutable)
    {
        $this->phpcsExecutable = $phpcsExecutable;
    }

    /**
     * @inheritDoc
     */
    public function run($sourceDirectory, $workDirectory)
    {
        $phpcsFilename = $this->ensureDirectoryWritable($workDirectory) . '/phpcs.xml';

        $processBuilder = new ProcessBuilder();
        $processBuilder
            ->add($this->phpcsExecutable)
            ->add('--extensions=php')
            ->add('--standard=PSR1')
            ->add('--standard=PSR2')
            ->add('--report-xml=' . $phpcsFilename)
            ->add($sourceDirectory);

        $process = $processBuilder->getProcess();

        $exitStatusCode = $process->run();

        if (!$process->isSuccessful() && $exitStatusCode != 1) {
            echo $process->getCommandLine().PHP_EOL;
            echo 'RC: ' . $exitStatusCode.PHP_EOL;
            echo 'Output: ' . $process->getOutput().PHP_EOL;
            echo 'Error output: ' . $process->getErrorOutput().PHP_EOL;
            throw new \Exception('phpcs execution resulted in an error');
        }

        return $phpcsFilename;
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
