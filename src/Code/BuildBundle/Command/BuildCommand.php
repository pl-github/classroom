<?php

namespace Code\BuildBundle\Command;

use Code\BuildBundle\Build;
use Code\BuildBundle\Builder;
use Code\BuildBundle\Comparer\Comparer;
use Code\BuildBundle\Loader\LoaderInterface as BuildLoaderInterface;
use Code\BuildBundle\Writer\WriterInterface as BuildWriterInterface;
use Code\ProjectBundle\Change\Loader\LoaderInterface as ChangeLoaderInterface;
use Code\ProjectBundle\Change\NewBuildChange;
use Code\ProjectBundle\Change\Writer\WriterInterface as ChangeWriterInterface;
use Code\ProjectBundle\Feed\Item;
use Code\ProjectBundle\Project;
use Code\ProjectBundle\Loader\LoaderInterface as ProjectLoaderInterface;
use Code\ProjectBundle\Writer\WriterInterface as ProjectWriterInterface;
use Code\RepositoryBundle\RepositoryFactory;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class BuildCommand extends ContainerAwareCommand
{
    protected $name;

    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('code:build:build')
            ->setDescription('Create build')
            ->addArgument('projectId', InputArgument::REQUIRED);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $projectId = $input->getArgument('projectId');

        $rootDir = $this->getContainer()->getParameter('kernel.root_dir');

        $entityManager = $this->getContainer()->get('doctrine.orm.entity_manager');
        /* @var $projectLoader EntityManager */
        $projectRepository = $entityManager->getRepository('Code\ProjectBundle\Entity\Project');
        $buildRepository = $entityManager->getRepository('Code\BuildBundle\Entity\Build');

        $builder = $this->getContainer()->get('code.build.builder');
        /* @var $builder Builder */

        $buildComparer = $this->getContainer()->get('code.build.comparer');
        /* @var $buildComparer Comparer */

        $changeLoader = $this->getContainer()->get('code.project.change.loader');
        /* @var $changeLoader ChangeLoaderInterface */
        $changeWriter = $this->getContainer()->get('code.project.change.writer');
        /* @var $changeWriter ChangeWriterInterface */

        $project = $projectRepository->findOneBy(array('name' => $projectId));

        $build = $builder->build($project);

        $project->setLatestBuildVersion($build->getVersion());

        /*
        if (!file_exists($rootDir . '/data/' . $projectId . '/changes.serialized')) {
            $changes = new \Code\ProjectBundle\Change\Changes();
        } else {
            $changes = $changeLoader->load($project);
        }

        $changes->addChange(new NewBuildChange($build->getVersion()));

        if ($project->getPreviousBuildVersion()) {
            $oldBuild = $buildLoader->load($project, $project->getPreviousBuildVersion());
            $changeSet = $buildComparer->compare($oldBuild, $build);
            $changes->mergeChangeSet($changeSet);
        }

        $changeWriter->write($changes, $project);
        */

        $entityManager->persist($build);
        $entityManager->persist($project);

        $output->writeln('Build for ' . $projectId . ' finished.');
    }
}
