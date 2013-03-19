<?php

namespace Code\AnalyzerBundle\Command;

use Code\AnalyzerBundle\Analyzer\AnalyzerInterface;
use Code\AnalyzerBundle\Writer\PharWriter;
use Code\AnalyzerBundle\Writer\SerializeWriter;
use Code\AnalyzerBundle\Writer\XmlWriter;
use Code\PhpAnalyzerBundle\ResultBuilder;
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
            ->setName('code:analyzer:write')
            ->setDescription('Analyze and write')
            ->addArgument('sourceDirectory', InputArgument::REQUIRED, 'Source directory')
            ->addArgument('targetFilename', InputArgument::REQUIRED, 'Target filename')
            ->addOption('xml', null, InputOption::VALUE_NONE, 'Write xml')
            ->addOption('phar', null, InputOption::VALUE_NONE, 'Write phar')
            ->addOption('serialize', null, InputOption::VALUE_NONE, 'Write serialized');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $sourceDirectory = $input->getArgument('sourceDirectory');
        $targetFilename = $input->getArgument('targetFilename');

        if ($input->getOption('xml')) {
            $writer = $this->getContainer()->get('code.analyzer.writer.xml');
            /* @var $writer XmlWriter */
        } elseif ($input->getOption('phar')) {
            $writer = $this->getContainer()->get('code.analyzer.writer.phar');
            /* @var $writer PharWriter */
        } else {
            $writer = $this->getContainer()->get('code.analyzer.writer.serialize');
            /* @var $writer SerializeWriter */
        }

        $entityManager = $this->getContainer()->get('doctrine.orm.entity_manager');
        /* @var $entityManager EntityManager */
        $projectRepository = $entityManager->getRepository('Code\ProjectBundle\Entity\Project');

        $resultBuilder = $this->getContainer()->get('code.php_analyzer.result_builder');
        /* @var $resultBuilder ResultBuilder */

        $analyzer = $this->getContainer()->get('code.php_analyzer.analyzer');
        /* @var $analyzer AnalyzerInterface */

        $tsStart = microtime(true);

        $result = $resultBuilder->bla(
            $analyzer,
            $sourceDirectory,
            '/opt/www/code/symfony/app/data/code/work'
        );

        //foreach ($result->getNodes() as $nodeId => $node) {
        //    /* @var $smell NodeInterface */
        //    echo '[node] ' . $nodeId . ' => ' . get_class($node) . PHP_EOL;
        //}

        echo $writer->write($result, dirname($targetFilename), basename($targetFilename)) . PHP_EOL;

        $duration = microtime(true) - $tsStart;

        $output->writeln(
            'Analyze run for ' . $sourceDirectory . ' finished, result written, ' .
            number_format($duration, 2) . ' s, ' .
            number_format(memory_get_usage() / 1024 / 1024, 2) . ' mb'
        );
    }
}
