<?php

namespace Code\ProjectBundle\Command;

use Code\ProjectBundle\Entity\Project;
use Code\RepositoryBundle\Entity\RepositoryConfig;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ListCommand extends ContainerAwareCommand
{
    protected $name;

    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('code:project:list')
            ->setDescription('list projects')
            ->addArgument('filter', InputArgument::OPTIONAL);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filter = $input->getArgument('filter');

        $entityManager = $this->getContainer()->get('doctrine.orm.entity_manager');
        /* @var $entityManager EntityManager */

        $repository = $entityManager->getRepository('Code\ProjectBundle\Entity\Project');

        if (strlen($filter)) {
            $projects = $repository->findBy(array('name' => $filter));
        } else {
            $projects = $repository->findAll();
        }

        $output->writeln(
            str_pad('Name', 20).
            str_pad('Type', 10).
            'Latest Build'
        );

        foreach ($projects as $project)
        {
            /* @var $project \Code\ProjectBundle\Entity\Project */

            $output->writeln(
                str_pad($project->getName(), 20) .
                str_pad($project->getRepositoryConfig()->getType(), 10) .
                ($project->getLatestBuildVersion() ? $project->getLatestBuildVersion() : '(No build)')
            );
        }

        return true;
    }
}
