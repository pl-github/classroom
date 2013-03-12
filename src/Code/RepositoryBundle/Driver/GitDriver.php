<?php

namespace Code\RepositoryBundle\Driver;

use Code\RepositoryBundle\RepositoryConfig;
use Symfony\Component\Process\ProcessBuilder;
use Symfony\Component\Process\Process;

class GitDriver implements DriverInterface
{
    /**
     * @var string
     */
    private $url;

    public function __construct(RepositoryConfig $repositoryConfig)
    {
        $this->url = $repositoryConfig->getUrl();
    }

    /**
     * @inheritDoc
     */
    public function checkout($checkoutDirectory)
    {
        $processBuilder = new ProcessBuilder();
        $processBuilder
            ->add('git')
            ->add('clone')
            ->add('--depth')->add('0')
            ->add($this->url)
            ->add($checkoutDirectory);

        $process = $processBuilder->getProcess();

        echo $process->getCommandLine().PHP_EOL;
        $process->run();
    }

    /**
     * @inheritDoc
     */
    public function getLastCommit($checkoutDirectory)
    {
        $processBuilder1 = new ProcessBuilder();
        $processBuilder1->add('cd')->add($checkoutDirectory);

        $processBuilder2 = new ProcessBuilder();
        $processBuilder2->add('git')->add('rev-parse')->add('HEAD');

        $process = new Process(
            $processBuilder1->getProcess()->getCommandLine()
            . '; '
            . $processBuilder2->getProcess()->getCommandLine()
        );

        echo $process->getCommandLine().PHP_EOL;
        $process->run();

        $commit = trim($process->getOutput());
        echo $commit.PHP_EOL;

        return $commit;
    }
}
