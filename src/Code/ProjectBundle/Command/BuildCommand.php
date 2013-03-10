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

        $copyPasteDetectionService = $this->getContainer()->get('code.copy_paste_detection.service');
        /* @var $service \Code\CopyPasteDetectionBundle\CopyPasteDetectionService */

        $metricsService = $this->getContainer()->get('code.metrics.service');
        /* @var $service \Code\MetricsBundle\MetricsService */

        $messDetectionService = $this->getContainer()->get('code.mess_detection.service');
        /* @var $service \Code\MessDetectionBundle\MessDetectionService */

        $projectLoader = $this->getContainer()->get('code.project.loader');
        /* @var $projectLoader ProjectLoaderInterface */
        $projectWriter = $this->getContainer()->get('code.project.writer');
        /* @var $projectWriter ProjectWriterInterface */

        $project = $projectLoader->load($projectId);
        $sourceDirectory = $project->getSourceDirectory();
        $workDirectory = $rootDir . '/data/' . $project->getId() . '/work';

        $copyPasteClasses = $copyPasteDetectionService->run($sourceDirectory, $workDirectory);
        $metricsClasses = $metricsService->run($sourceDirectory, $workDirectory);
        $messClasses = $messDetectionService->run($sourceDirectory, $workDirectory);

        $classesMerger = $this->getContainer()->get('code.project.merge.classes_merge');
        $classes = $classesMerger->merge($copyPasteClasses, $metricsClasses, $messClasses);

        $buildGenerator = $this->getContainer()->get('code.project.build.generator');
        /* @var $buildGenerator BuildGenerator */

        $build = $buildGenerator->createBuild($project);
        $build->setClasses($classes);

        $buildLoader = $this->getContainer()->get('code.project.build.loader');
        /* @var $buildLoader BuildLoaderInterface */
        $buildWriter = $this->getContainer()->get('code.project.build.writer');
        /* @var $buildWriter BuildWriterInterface */

        $buildWriter->write($build);
        $project->getFeed()->addItem(new Item('New build ' . $build->getVersion()));
        $project->setLatestBuildVersion($build->getVersion());

        if ($project->getPreviousBuildVersion()) {
            $comparer = $this->getContainer()->get('code.project.build.comparer');
            /* @var $comparer Comparer */
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
