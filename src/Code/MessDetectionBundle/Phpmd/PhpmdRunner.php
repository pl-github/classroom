<?php

namespace Code\MessDetectionBundle\Phpmd;

use Code\AnalyzerBundle\Analyzer\Runner\RunnerInterface;
use Symfony\Component\Process\ProcessBuilder;
use Symfony\Component\Process\Process;

class PhpmdRunner implements RunnerInterface
{
    private $phpmdExecutable;

    /**
     * @param $phpcpdExecutable
     */
    public function __construct($phpmdExecutable)
    {
        $this->phpmdExecutable = $phpmdExecutable;
    }

    /**
     * @inheritDoc
     */
    public function run($sourceDirectory, $workDirectory)
    {
        $phpmdFilename = $this->ensureDirectoryWritable($workDirectory) . '/phpmd.xml';

        $processBuilder = new ProcessBuilder();
        $processBuilder->add($this->phpmdExecutable)
            ->add($sourceDirectory)
            ->add('xml')
            ->add('codesize,unusedcode,naming')
            ->add('--suffixes')->add('php')
            ->add('--reportfile')->add($phpmdFilename);

        $process = $processBuilder->getProcess();

        $exitStatusCode = $process->run();

        if (!$process->isSuccessful() && $exitStatusCode != 2) {
            echo $process->getCommandLine().PHP_EOL;
            echo 'RC: ' . $exitStatusCode.PHP_EOL;
            echo 'Output: ' . $process->getOutput().PHP_EOL;
            echo 'Error output: ' . $process->getErrorOutput().PHP_EOL;
            throw new \Exception('pdepend execution resulted in an error');
        }

        return $phpmdFilename;
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
