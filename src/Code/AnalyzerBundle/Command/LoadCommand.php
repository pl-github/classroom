<?php

namespace Code\AnalyzerBundle\Command;

use Code\AnalyzerBundle\Analyzer\AnalyzerInterface;
use Code\AnalyzerBundle\Loader\PharLoader;
use Code\AnalyzerBundle\Loader\SerializeLoader;
use Code\AnalyzerBundle\Loader\XmlLoader;
use Code\PhpAnalyzerBundle\ResultBuilder;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class LoadCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('code:analyzer:load')
            ->setDescription('Load and output')
            ->addArgument('filename', InputArgument::REQUIRED, 'Filename')
            ->addOption('xml', null, InputOption::VALUE_NONE, 'Write xml')
            ->addOption('phar', null, InputOption::VALUE_NONE, 'Write phar')
            ->addOption('serialize', null, InputOption::VALUE_NONE, 'Write serialized');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filename = $input->getArgument('filename');

        if ($input->getOption('xml')) {
            $loader = $this->getContainer()->get('code.analyzer.loader.xml');
            /* @var $loader XmlLoader */
        } elseif ($input->getOption('phar')) {
            $loader = $this->getContainer()->get('code.analyzer.loader.phar');
            /* @var $loader PharLoader */
        } else {
            $loader = $this->getContainer()->get('code.analyzer.loader.serialize');
            /* @var $loader SerializeLoader */
        }

        $result = $loader->load($filename);

        foreach ($result->getSmells() as $smell) {
            /* @var $smell SmellModel */
            $sourceRange = $smell->getSourceRange();
            $beginLine = $sourceRange->getBeginLine();
            $endLine = $sourceRange->getEndLine();

            $classReference = $result->getOutgoing('smell', $smell);
            $classNode = $result->getNode($classReference);
            $fileReference = $result->getOutgoing('node', $classNode);
            $fileNode = $result->getNode($fileReference);

            $sourceReference = $result->getIncoming('source', $fileNode);
            $source = $result->getSource($sourceReference);

            echo '[smell] ' . $smell->getRule() . ' in ' . $classNode->getName() . ':' .
                $beginLine . ($beginLine != $endLine ? '-' . $endLine : '') . PHP_EOL;

            echo '<code> ' . PHP_EOL . $source->getRange($sourceRange) . PHP_EOL . '</code>' . PHP_EOL;
        }

        $output->writeln(
            'Load, ' .
            number_format(memory_get_usage() / 1024 / 1024, 2) . ' mb'
        );
    }
}
