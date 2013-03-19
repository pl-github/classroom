<?php

namespace Code\PhpAnalyzerBundle\Command;

use Code\AnalyzerBundle\Analyzer\AnalyzerInterface;
use Code\AnalyzerBundle\Model\SmellModel;
use Code\AnalyzerBundle\Writer\XmlWriter;
use Code\BuildBundle\Build;
use Code\PhpAnalyzerBundle\ResultBuilder;
use Code\ProjectBundle\Project;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AnalyzeCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('code:php:analyze')
            ->setDescription('Analyze PHP')
            ->addArgument('projectId', InputArgument::REQUIRED);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $projectId = $input->getArgument('projectId');

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
            '/opt/www/code/symfony/src/Code',
            '/opt/www/code/symfony/app/data/code/work'
        );

        //foreach ($result->getNodes() as $nodeId => $node) {
        //    /* @var $smell NodeInterface */
        //    echo '[node] ' . $nodeId . ' => ' . get_class($node) . PHP_EOL;
        //}

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

            echo '[smell] ' . $smell->getRule() . ' in ' . $classNode->getFullQualifiedName() . ':' .
                 $beginLine . ($beginLine != $endLine ? '-' . $endLine : '') . PHP_EOL;

            echo '<code> ' . PHP_EOL . $source->getRange($sourceRange) . PHP_EOL . '</code>' . PHP_EOL;
        }

        $duration = microtime(true) - $tsStart;

        $output->writeln(
            'Analyze run for ' . $projectId . ' finished, ' .
            number_format($duration, 2) . ' s, ' .
            number_format(memory_get_usage() / 1024 / 1024, 2) . ' mb'
        );
    }
}
