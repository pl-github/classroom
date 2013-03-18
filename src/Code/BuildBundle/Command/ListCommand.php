<?php

namespace Code\BuildBundle\Command;

use Code\BuildBundle\Entity\Build;
use JMS\JobQueueBundle\Entity\Job;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('code:build:list')
            ->setDescription('List builds')
            ->addArgument('projectId', InputArgument::REQUIRED);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $projectId = $input->getArgument('projectId');

        $entityManager = $this->getContainer()->get('doctrine.orm.entity_manager');
        /* @var $entityManager \Doctrine\ORM\EntityManager */

        $repository = $entityManager->getRepository('Code\BuildBundle\Entity\Build');

        $builds = $repository->findBy(array(), array('createdAt' => 'DESC'));

        $output->writeln('Builds for ' . $projectId . ':');

        $output->writeln(
            str_pad('Version', 10) .
            str_pad('Date', 20) .
            'GPA'
        );

        foreach ($builds as $build) {
            /* @var $build Build */

            $output->writeln(
                str_pad($build->getVersion(), 10) .
                $build->getCreatedAt()->format('Y-m-d H:i:s'),
                $build->getGpa()
            );
        }
    }
}
