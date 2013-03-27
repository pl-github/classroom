<?php

namespace Classroom\AnalyzerBundle;

use Symfony\Component\Process\Process;

class ProcessExecutor
{
    /**
     * Execute command
     *
     * @param Process $command
     * @param integer $allowedExitStatusCode
     * @throws \Exception
     */
    public function execute(Process $process, $allowedExitStatusCode)
    {
        $process->setTimeout(120);

        $exitStatusCode = $process->run();

        if (!$process->isSuccessful() && $exitStatusCode != $allowedExitStatusCode) {
            echo $process->getCommandLine().PHP_EOL;
            echo 'RC: ' . $exitStatusCode.PHP_EOL;
            echo 'Output: ' . $process->getOutput().PHP_EOL;
            echo 'Error output: ' . $process->getErrorOutput().PHP_EOL;
            throw new \Exception('phpcs execution resulted in an error');
        }
    }
}
