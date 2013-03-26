<?php

namespace Classroom\ProjectBundle\Command;

use Classroom\ProjectBundle\Entity\Revision;
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
            ->setName('classroom:project:queue')
            ->setDescription('Queue revision')
            ->addArgument('revisionId');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $revisionId = $input->getArgument('revisionId');

        $entityManager = $this->getContainer()->get('doctrine.orm.entity_manager');

        $job = new Job('classroom:project:build-revision', array($revisionId));
        $entityManager->persist($job);
        $entityManager->flush($job);

        $output->writeln('Build for revision id ' . $revisionId . ' queued.');
    }
}
