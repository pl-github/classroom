<?php

namespace Code\ProjectBundle\Command;

use Code\ProjectBundle\Build\Build;
use Code\ProjectBundle\Build\Writer\SerializeWriter;
use Code\ProjectBundle\Project;
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
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $rootDir = $this->getContainer()->getParameter('kernel.root_dir');

        $copyPasteDetectionService = $this->getContainer()->get('code.copy_paste_detection.service');
        /* @var $service \Code\CopyPasteDetectionBundle\CopyPasteDetectionService */

        $metricsService = $this->getContainer()->get('code.metrics.service');
        /* @var $service \Code\MetricsBundle\MetricsService */

        $messDetectionService = $this->getContainer()->get('code.mess_detection.service');
        /* @var $service \Code\MessDetectionBundle\MessDetectionService */

        $sourceDirectory = dirname(dirname(__DIR__));

        $copyPasteClasses = $copyPasteDetectionService->run($sourceDirectory);
        $metricsClasses = $metricsService->run($sourceDirectory);
        $messClasses = $messDetectionService->run($sourceDirectory);

        $classesMerger = $this->getContainer()->get('code.project.merge.classes_merge');
        $classes = $classesMerger->merge($copyPasteClasses, $metricsClasses, $messClasses);

        $buildGenerator = $this->getContainer()->get('code.project.build.generator');
        /* @var $buildGenerator \Code\ProjectBundle\Build\BuildGenerator */

        $project = new Project(1, 'swentz');

        $build = $buildGenerator->createBuild($project);
        $build->setClasses($classes);

        $writer = $this->getContainer()->get('code.project.build.writer');
        /* @var $writer \Code\ProjectBundle\Build\Writer\WriterInterface */
        $writer->write($build);
    }
}
