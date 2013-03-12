<?php

namespace Code\ProjectBundle\Command;

use Code\ProjectBundle\Build\Build;
use Code\ProjectBundle\Build\BuildGenerator;
use Code\ProjectBundle\Build\Builder;
use Code\ProjectBundle\Build\Comparer\Comparer;
use Code\ProjectBundle\Build\Loader\LoaderInterface as BuildLoaderInterface;
use Code\ProjectBundle\Build\Writer\WriterInterface as BuildWriterInterface;
use Code\ProjectBundle\Change\Loader\LoaderInterface as ChangeLoaderInterface;
use Code\ProjectBundle\Change\NewBuildChange;
use Code\ProjectBundle\Change\Writer\WriterInterface as ChangeWriterInterface;
use Code\ProjectBundle\Feed\Item;
use Code\ProjectBundle\Project;
use Code\ProjectBundle\Loader\LoaderInterface as ProjectLoaderInterface;
use Code\ProjectBundle\Writer\WriterInterface as ProjectWriterInterface;
use Code\RepositoryBundle\RepositoryFactory;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
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
            ->setName('code:project:build')
            ->setDescription('Build project')
            ->addArgument('projectId');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $projectId = $input->getArgument('projectId');

        $rootDir = $this->getContainer()->getParameter('kernel.root_dir');

        $projectLoader = $this->getContainer()->get('code.project.loader');
        /* @var $projectLoader ProjectLoaderInterface */
        $projectWriter = $this->getContainer()->get('code.project.writer');
        /* @var $projectWriter ProjectWriterInterface */

        $builder = $this->getContainer()->get('code.project.build.builder');
        /* @var $builder Builder */

        $buildLoader = $this->getContainer()->get('code.project.build.loader');
        /* @var $buildLoader BuildLoaderInterface */
        $buildWriter = $this->getContainer()->get('code.project.build.writer');
        /* @var $buildWriter BuildWriterInterface */

        $comparer = $this->getContainer()->get('code.project.build.comparer');
        /* @var $comparer Comparer */

        $changeLoader = $this->getContainer()->get('code.project.change.loader');
        /* @var $changeLoader ChangeLoaderInterface */
        $changeWriter = $this->getContainer()->get('code.project.change.writer');
        /* @var $changeWriter ChangeWriterInterface */

        $project = $projectLoader->load($projectId);

        $build = $builder->build($project);

        $project->setLatestBuildVersion($build->getVersion());

        if (!file_exists($rootDir . '/data/' . $projectId . '/changes.serialized')) {
            $changes = new \Code\ProjectBundle\Change\Changes();
        } else {
            $changes = $changeLoader->load($project);
        }

        $changes->addChange(new NewBuildChange($build->getVersion()));

        if ($project->getPreviousBuildVersion()) {
            $oldBuild = $buildLoader->load($project, $project->getPreviousBuildVersion());
            $changeSet = $comparer->compare($oldBuild, $build);
            $changes->mergeChangeSet($changeSet);
        }

        $changeWriter->write($changes, $project);

        $buildWriter->write($build);
        $projectWriter->write($project);
    }
}
