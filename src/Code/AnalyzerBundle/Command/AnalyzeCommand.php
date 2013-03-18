<?php

namespace Code\AnalyzerBundle\Command;

use Code\AnalyzerBundle\PharBuilder;
use Code\AnalyzerBundle\ResultBuilder;
use Code\BuildBundle\Build;
use Code\ProjectBundle\Project;
use Code\ProjectBundle\Loader\LoaderInterface as ProjectLoaderInterface;
use Code\ProjectBundle\Writer\WriterInterface as ProjectWriterInterface;
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
            ->setName('code:analyzer:analyze')
            ->setDescription('Analyze')
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

        $resultBuilder = $this->getContainer()->get('code.analyzer.result_builder');
        /* @var $resultBuilder ResultBuilder */

        //$project = $projectRepository->findOneBy(array('name' => $projectId));

        $result = $resultBuilder->createResult('/opt/www/code/symfony/src/Code', '/opt/www/code/symfony/app/data/code/work');

        foreach ($result->getNodes() as $nodeId => $node)
        {
            /* @var $smell NodeInterface */
            echo '[node] ' . $nodeId . ' => ' . get_class($node) . PHP_EOL;
        }

        foreach ($result->getSmells() as $smellId => $smell)
        {
            /* @var $smell SmellModel */
            echo '[smell] ' . $smellId . ' => ' . $smell->getRule() . PHP_EOL;

            $node = $result->getNode($smell->getNodeReference());
            echo '  ' . $node->getFullQualifiedName().PHP_EOL;
        }

        $pharBuilder = new PharBuilder();
        $pharBuilder->createPhar($result);

        $output->writeln('Analyze for ' . $projectId . ' finished.');
    }
}
