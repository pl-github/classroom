<?php

namespace Code\BuildBundle\Command;

use Code\BuildBundle\Build;
use JMS\JobQueueBundle\Entity\Job;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class QueueCommand extends ContainerAwareCommand
{
    protected $name;

    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('code:build:queue')
            ->setDescription('Queue build')
            ->addArgument('projectId');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $projectId = $input->getArgument('projectId');

        $entityManager = $this->getContainer()->get('doctrine.orm.entity_manager');

        $job = new Job('code:build:build', array($projectId));
        $entityManager->persist($job);
        $entityManager->flush($job);

        $output->writeln('Build for ' . $projectId . ' queued.');
    }
}
