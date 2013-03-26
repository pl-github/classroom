<?php

namespace Classroom\RepositoryBundle;

use Classroom\ProjectBundle\DataDir;
use Classroom\RepositoryBundle\Entity\RepositoryConfig;
use Classroom\RepositoryBundle\VersionStrategy\VersionStrategyInterface;
use Symfony\Component\Process\ProcessBuilder;
use Symfony\Component\Process\Process;

class GitRepository implements RepositoryInterface
{
    /**
     * @var RepositoryConfig
     */
    private $repositoryConfig;

    /**
     * @var DataDir
     */
    private $dataDir;

    /**
     * @var string
     */
    private $workDir;

    /**
     * @param RepositoryConfig $repositoryConfig
     * @param DataDir          $dataDir
     */
    public function __construct(RepositoryConfig $repositoryConfig, DataDir $dataDir)
    {
        $this->repositoryConfig = $repositoryConfig;
        $this->dataDir = $dataDir;

        $this->workDir = $this->dataDir->getBaseDirectory() . '/git';
    }

    /**
     * @inheritDoc
     */
    public function getSourceDirectory()
    {
        return $this->gitClone();
    }

    /**
     * @inheritDoc
     */
    public function determineVersion()
    {
        return $this->gitRevParse();
    }


    /**
     * @inheritDoc
     */
    public function determineBranch()
    {
        return $this->gitBranch();
    }

    /**
     * @return string
     */
    private function gitClone()
    {
        $processBuilder = new ProcessBuilder();
        $processBuilder
            ->add('git')
            ->add('clone')
            ->add('--depth')->add('0')
            ->add($this->repositoryConfig->getUrl())
            ->add($this->workDir);

        $process = $processBuilder->getProcess();

        echo $process->getCommandLine().PHP_EOL;
        $process->run();
        echo $process->getOutput().PHP_EOL;
        echo $process->getErrorOutput().PHP_EOL;

        return $this->workDir;
    }

    /**
     * @return string
     */
    private function gitBranch()
    {
        \chdir($this->workDir);

        $processBuilder = new ProcessBuilder();
        $processBuilder
            ->add('git')
            ->add('branch');

        $process = $processBuilder->getProcess();

        echo $process->getCommandLine().PHP_EOL;
        $process->run();
        echo $process->getOutput().PHP_EOL;
        echo $process->getErrorOutput().PHP_EOL;

        $branch = trim($process->getOutput());
        echo 'Branch: '.$branch.PHP_EOL;

        return $branch;
    }

    /**
     * @return string
     */
    private function gitRevParse()
    {
        \chdir($this->workDir);

        $processBuilder = new ProcessBuilder();
        $processBuilder->add('git')->add('rev-parse')->add('HEAD');

        $process = $processBuilder->getProcess();

        echo $process->getCommandLine().PHP_EOL;
        $process->run();
        echo $process->getOutput().PHP_EOL;
        echo $process->getErrorOutput().PHP_EOL;

        $commit = trim($process->getOutput());
        echo 'Commit ID: '.$commit.PHP_EOL;

        return $commit;
    }
}
