<?php

namespace Code\MetricsBundle\Pdepend;

use Symfony\Component\Process\ProcessBuilder;
use Symfony\Component\Process\Process;

class PdependExecutor
{
    /**
     * @param string $sourceDirectory
     * @param string $workDirectory
     * @return string
     * @throws \Exception
     */
    public function execute($sourceDirectory, $workDirectory)
    {
        $summaryFilename = $this->ensureDirectoryWritable($workDirectory) . '/pdepend-summary.xml';

        $processBuilder = new ProcessBuilder();
        $processBuilder->add('pdepend')
            ->add('--summary-xml=' . $summaryFilename)
            ->add($sourceDirectory);

        $process = $processBuilder->getProcess();

        $exitStatusCode = $process->run();

        if (!$process->isSuccessful() && $exitStatusCode != 1) {
            echo $process->getCommandLine().PHP_EOL;
            echo 'RC: ' . $exitStatusCode.PHP_EOL;
            echo 'Output: ' . $process->getOutput().PHP_EOL;
            echo 'Error output: ' . $process->getErrorOutput().PHP_EOL;
            throw new \Exception('pdepend execution resulted in an error');
        }

        return $summaryFilename;
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
