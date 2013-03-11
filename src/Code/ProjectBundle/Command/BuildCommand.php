<?php

namespace Code\ProjectBundle\Command;

use Code\ProjectBundle\Build\Build;
use Code\ProjectBundle\Build\BuildGenerator;
use Code\ProjectBundle\Build\Comparer\Comparer;
use Code\ProjectBundle\Build\Loader\LoaderInterface as BuildLoaderInterface;
use Code\ProjectBundle\Build\Writer\WriterInterface as BuildWriterInterface;
use Code\ProjectBundle\Feed\Item;
use Code\ProjectBundle\Project;
use Code\ProjectBundle\Loader\LoaderInterface as ProjectLoaderInterface;
use Code\ProjectBundle\Writer\WriterInterface as ProjectWriterInterface;
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
            ->addArgument('projectId')
        ;
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
        $analyzer = $this->getContainer()->get('code.analyzer.chain_analyzer');
        /* @var $analyzer \Code\AnalyzerBundle\ChainAnalyzer */
        $buildGenerator = $this->getContainer()->get('code.project.build.generator');
        /* @var $buildGenerator BuildGenerator */
        $buildLoader = $this->getContainer()->get('code.project.build.loader');
        /* @var $buildLoader BuildLoaderInterface */
        $buildWriter = $this->getContainer()->get('code.project.build.writer');
        /* @var $buildWriter BuildWriterInterface */
        $comparer = $this->getContainer()->get('code.project.build.comparer');
        /* @var $comparer Comparer */

        $project = $projectLoader->load($projectId);
        $sourceDirectory = $project->getSourceDirectory();
        $workDirectory = $rootDir . '/data/' . $project->getId() . '/work';

        $classes = $analyzer->analyze($sourceDirectory, $workDirectory);

        $build = $buildGenerator->createBuild($project);
        $build->setClasses($classes);

        $buildWriter->write($build);
        $project->getFeed()->addItem(new Item('New build ' . $build->getVersion()));
        $project->setLatestBuildVersion($build->getVersion());

        if ($project->getPreviousBuildVersion()) {
            $oldBuild = $buildLoader->load($project, $project->getPreviousBuildVersion());
            $changeSet = $comparer->compare($oldBuild, $build);

            foreach ($changeSet->getChanges() as $change)
            {
                $project->getFeed()->addItem(new Item('Change detected in ' . $change->getClass()->getName() . ', ' . $change->getText()));
            }
        }

        $projectWriter->write($project);
    }
}
