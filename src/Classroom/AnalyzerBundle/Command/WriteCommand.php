<?php

namespace Classroom\AnalyzerBundle\Command;

use Classroom\AnalyzerBundle\Writer\WriterInterface;
use Classroom\PhpAnalyzerBundle\ResultBuilder;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class WriteCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('classroom:analyzer:write')
            ->setDescription('Analyze and write')
            ->addArgument('sourceDirectory', InputArgument::REQUIRED, 'Source directory')
            ->addArgument('filename', InputArgument::REQUIRED, 'Filename');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $sourceDirectory = realpath($input->getArgument('sourceDirectory'));
        $filename = $input->getArgument('filename');

        if (!$sourceDirectory) {
            $output->writeln('Invalid source directory ' . $sourceDirectory);
            return 1;
        }

        if (file_exists($filename)) {
            $output->writeln('Target filename ' . $sourceDirectory . ' already exists');
            return 1;
        }

        $writer = $this->getContainer()->get('classroom.analyzer.writer');
        /* @var $writer WriterInterface */

        $resultBuilder = $this->getContainer()->get('classroom.php_analyzer.result_builder');
        /* @var $resultBuilder ResultBuilder */


        $output->write('building... ');
        $tsBuildStart = microtime(true);
        $result = $resultBuilder->buildResult($sourceDirectory, sys_get_temp_dir());
        $tsBuildEnd = microtime(true);
        $output->writeln('done ('.number_format($tsBuildEnd - $tsBuildStart, 2).' s)');

        $output->write('writing...');
        $tsWriteStart = microtime(true);
        $writer->write($result, $filename);
        $tsWriteEnd = microtime(true);
        $output->writeln('done ('.number_format($tsWriteEnd - $tsWriteStart, 2).' s)');

        $duration = $tsWriteEnd - $tsBuildStart;

        $output->writeln(
            'Analyze run for ' . $sourceDirectory . ' finished, result written in ' . $filename . ', '.
            'filesize ' . number_format(filesize($filename) / 1024, 2) . ' kb'
        );
        $output->writeln(
            'Duration ' . number_format($duration, 2) . ' s, '.
            'memory usage ' . number_format(memory_get_usage() / 1024 / 1024, 2) . ' mb, ' .
            'memory peak usage ' . number_format(memory_get_peak_usage() / 1024 / 1024, 2) . ' mb'
        );
    }
}
