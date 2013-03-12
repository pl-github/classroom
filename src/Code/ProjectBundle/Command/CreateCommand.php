<?php

namespace Code\ProjectBundle\Command;

use Code\ProjectBundle\Feed\Item;
use Code\ProjectBundle\Writer\WriterInterface;
use Code\ProjectBundle\Project;
use Code\RepositoryBundle\RepositoryConfig;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateCommand extends ContainerAwareCommand
{
    protected $name;

    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('code:project:create')
            ->setDescription('Create project')
            ->addArgument('id')
            ->addArgument('name')
            ->addArgument('libDir')
            ->addArgument('type')
            ->addArgument('url');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $id = $input->getArgument('id');
        $name = $input->getArgument('name');
        $libDir = $input->getArgument('libDir');
        $type = $input->getArgument('type');
        $url = $input->getArgument('url');

        $repositoryConfig = new RepositoryConfig($type, $url);

        $project = new Project($id, $name, $libDir, $repositoryConfig);
        //$project->getFeed()->addItem(new Item('Project "' . $name . '" created.', new \DateTime));

        $writer = $this->getContainer()->get('code.project.writer');
        /* @var $writer WriterInterface */
        $writer->write($project);
    }
}
