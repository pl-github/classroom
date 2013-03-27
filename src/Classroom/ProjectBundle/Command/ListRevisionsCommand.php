<?php

namespace Classroom\ProjectBundle\Command;

use Classroom\ProjectBundle\Entity\Revision;
use JMS\JobQueueBundle\Entity\Job;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListRevisionsCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('classroom:project:list-revisions')
            ->setDescription('List revisions')
            ->addArgument('projectKey', InputArgument::REQUIRED);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $projectKey = $input->getArgument('projectKey');

        $entityManager = $this->getContainer()->get('doctrine.orm.entity_manager');
        /* @var $entityManager \Doctrine\ORM\EntityManager */

        $projectRepository = $entityManager->getRepository('Classroom\ProjectBundle\Entity\Revision');
        $revisionRepository = $entityManager->getRepository('Classroom\ProjectBundle\Entity\Revision');

        $project = $projectRepository->findOneBy(array('key' => $projectKey));
        $revisions = $revisionRepository->findBy(array('project' => $project), array('createdAt' => 'DESC'));

        $output->writeln('Builds for ' . $projectKey . ':');

        $output->writeln(
            str_pad('Version', 10) .
            str_pad('Date', 20) .
            'GPA'
        );

        foreach ($revisions as $revision) {
            /* @var $revision Revision */

            $output->writeln(
                str_pad($revision->getRevision(), 10) .
                $revision->getCreatedAt()->format('Y-m-d H:i:s'),
                $revision->getGpa()
            );
        }
    }
}
