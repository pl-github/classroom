<?php

namespace Code\BuildBundle\Command;

use Code\BuildBundle\Builder;
use Code\BuildBundle\Comparer\Comparer;
use Code\BuildBundle\Entity\Build;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
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
            ->setName('code:build:create')
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

        $project = $projectRepository->findOneBy(array('name' => $projectId));

        $build = new Build();
        $build
            ->setProject($project)
            ->setStatus(Build::STATUS_NEW)
            ->setRevision(1)
            ->setCreateAt(new \DateTime());

        $entityManager->persist($build);
        $entityManager->flush($build);

        $builder = $this->getContainer()->get('code.build.builder');
        /* @var $builder Builder */

        $buildComparer = $this->getContainer()->get('code.build.comparer');
        /* @var $buildComparer Comparer */

        $changeLoader = $this->getContainer()->get('code.project.change.loader');
        /* @var $changeLoader ChangeLoaderInterface */
        $changeWriter = $this->getContainer()->get('code.project.change.writer');
        /* @var $changeWriter ChangeWriterInterface */

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
