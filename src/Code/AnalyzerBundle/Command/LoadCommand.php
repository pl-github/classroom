<?php

namespace Code\AnalyzerBundle\Command;

use Code\AnalyzerBundle\Analyzer\AnalyzerInterface;
use Code\PhpAnalyzerBundle\ResultBuilder;
use Code\AnalyzerBundle\Loader\LoaderInterface;
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
            ->addOption('smells', null, InputOption::VALUE_NONE, 'Show smells')
            ->addOption('source', null, InputOption::VALUE_NONE, 'Show source');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filename = $input->getArgument('filename');
        $showSmells = $input->getOption('smells');
        $showSource = $input->getOption('source');

        $loader = $this->getContainer()->get('code.analyzer.loader');
        /* @var $loader LoaderInterface */

        $result = $loader->load($filename);

        if ($showSmells) {
            foreach ($result->getSmells() as $smell) {
                /* @var $smell SmellModel */
                $sourceRange = $smell->getSourceRange();
                $beginLine = $sourceRange->getBeginLine();
                $endLine = $sourceRange->getEndLine();

                $classReference = $result->getReference('smell', 'smellToNode', $smell);
                $classNode = $result->getNode($classReference);
                $output->writeln(
                    '[smell] <comment>' . $smell->getRule() . '</comment> in <info>' . $classNode->getName() . ' </info>' .
                    'line ' . $beginLine . ($beginLine != $endLine ? ':' . $endLine : '')
                );

                if ($showSource) {
                    $fileReference = $result->getReference('node', 'parent', $classNode);
                    $fileNode = $result->getNode($fileReference);

                    $sourceReference = $result->getReference('source', 'nodeToSource', $fileNode);
                    $source = $result->getSource($sourceReference);

                    $output->writeln(trim($source->getRange($sourceRange)) . PHP_EOL);
                }
            }
        } else {
            $output->writeln(count($result->getSmells()) . ' smells.');
        }

        $output->writeln(
            'Load, ' .
            number_format(memory_get_usage() / 1024 / 1024, 2) . ' mb'
        );
    }
}
