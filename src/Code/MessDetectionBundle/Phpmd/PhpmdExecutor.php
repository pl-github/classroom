<?php

namespace Code\MessDetectionBundle\Phpmd;

use Symfony\Component\Process\ProcessBuilder;
use Symfony\Component\Process\Process;

class PhpmdExecutor
{
    private $dataDir;

    public function __construct($rootDir)
    {
        $this->dataDir = $rootDir . '/data';

        if (!file_exists($this->dataDir) && !mkdir($this->dataDir, 0777, true)) {
            throw new \Exception('Can\'t create data dir');
        }
    }

    public function execute($dir)
    {
        $phpmdFilename = $this->dataDir . '/phpmd.xml';

        $processBuilder = new ProcessBuilder();
        $processBuilder->add('phpmd')
            ->add($dir)
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
}
