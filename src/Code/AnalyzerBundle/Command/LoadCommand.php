<?php

namespace Code\AnalyzerBundle\Command;

use Code\AnalyzerBundle\Analyzer\AnalyzerInterface;
use Code\AnalyzerBundle\Grader\GpaCalculator;
use Code\AnalyzerBundle\Grader\GraderInterface;
use Code\AnalyzerBundle\Model\ResultModel;
use Code\AnalyzerBundle\Node\Gradable;
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
            ->addOption('source', null, InputOption::VALUE_NONE, 'Show source')
            ->addOption('rule', null, InputOption::VALUE_REQUIRED, 'Smell rule filter')
            ->addOption('score', null, InputOption::VALUE_NONE, 'Show score');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filename = $input->getArgument('filename');
        $showSmells = $input->getOption('smells');
        $showSource = $input->getOption('source');
        $rule = $input->getOption('rule');
        $showScore = $input->getOption('score');

        $loader = $this->getContainer()->get('code.analyzer.loader');
        /* @var $loader LoaderInterface */

        $tsStart = microtime(true);
        $result = $loader->load($filename);
        $tsEnd = microtime(true);

        $output->writeln(
            'Load finished, ' .
            number_format($tsEnd - $tsStart, 2) . ' s, ' .
            number_format(memory_get_usage() / 1024 / 1024, 2) . ' mb'
        );

        if ($showSmells) {
            $this->showSmells($output, $result, $showSource, $rule);
        } elseif ($showScore) {
            $this->showScore($output, $result);
        } else {
            $this->showGpa($output, $result);
        }
    }

    private function showGpa(OutputInterface $output, ResultModel $result)
    {
        $gpaCalculator = $this->getContainer()->get('code.analyzer.gpa_calculator');
        /* @var $gpaCalculator GpaCalculator */

        $output->writeln('');
        $output->writeln('===========');
        $output->writeln(' Breakdown');
        $output->writeln('===========');
        $gradeMap = $gpaCalculator->buildGradeMap($result);
        foreach ($gradeMap as $grade => $count) {
            $output->writeln('  ' . $grade . ': ' . $count);
        }

        $gpa = $gpaCalculator->calculate($result);

        $output->writeln('');
        $output->writeln('===========');
        $output->writeln(' GPA: ' . number_format($gpa, 2));
        $output->writeln('===========');
        $output->writeln('');
    }

    private function showScore(OutputInterface $output, ResultModel $result)
    {
        $grader = $this->getContainer()->get('code.analyzer.grader');
        /* @var $grader GraderInterface */

        $output->writeln(
            str_pad('CLASS', 80) .
            str_pad('SMLLS', 6, ' ', STR_PAD_LEFT) .
            str_pad('SCORE', 6, ' ', STR_PAD_LEFT) .
            str_pad('LOC', 6, ' ', STR_PAD_LEFT) .
            str_pad('FACTOR', 18, ' ', STR_PAD_LEFT) .
            str_pad('WEIGHTED SCORE', 18, ' ', STR_PAD_LEFT) .
            str_pad('GRADE', 6, ' ', STR_PAD_LEFT)
        );

        foreach ($result->getNodes() as $node) {
            if (!$node instanceof Gradable) {
                continue;
            }

            $smells = array();
            if ($result->hasReference('smell', 'nodeToSmells', $node)) {
                $smellReferences = $result->getReference('smell', 'nodeToSmells', $node);

                foreach ($smellReferences as $smellReference) {
                    $smells[] = $result->getSmell($smellReference);
                }
            }

            $score = $grader->getScore($smells);
            $linesOfCode = $grader->getLinesOfCode($node);
            $factor = $grader->getFactor($linesOfCode);
            $weightedScore = $grader->getWeightedScore($score, $factor);
            $grade = $grader->scoreToGrade($score);

            $output->writeln(
                str_pad($node->getName(), 80) .
                str_pad(count($smells), 6, ' ', STR_PAD_LEFT) .
                str_pad($score, 6, ' ', STR_PAD_LEFT) .
                str_pad($linesOfCode, 6, ' ', STR_PAD_LEFT) .
                str_pad($factor, 18, ' ', STR_PAD_LEFT) .
                str_pad($weightedScore, 18, ' ', STR_PAD_LEFT) .
                str_pad($grade, 6, ' ', STR_PAD_LEFT)
            );
        }
    }

    private function showSmells(OutputInterface $output, ResultModel $result, $showSource = false, $rule = null)
    {
        foreach ($result->getSmells() as $smell) {
            /* @var $smell SmellModel */
            $sourceRange = $smell->getSourceRange();
            $beginLine = $sourceRange->getBeginLine();
            $endLine = $sourceRange->getEndLine();

            if ($rule && stripos($smell->getRule(), $rule) === false) {
                continue;
            }

            $classReference = $result->getReference('smell', 'smellToNode', $smell);
            $classNode = $result->getNode($classReference);
            $output->writeln(
                '[smell] <comment>' . $smell->getRule() . '</comment> (' . $smell->getScore() . ') '.
                'in <info>' . $classNode->getName() . ' </info>' .
                'line ' . $beginLine . ($beginLine != $endLine ? ':' . $endLine : '')
            );

            if ($showSource) {
                $fileReference = $result->getReference('node', 'parent', $classNode);
                $fileNode = $result->getNode($fileReference);

                $sourceReference = $result->getReference('source', 'nodeToSource', $fileNode);
                $source = $result->getSource($sourceReference);

                $lines = $source->getRangeAsArray($sourceRange);
                foreach ($lines as $lineNo => $line) {
                    $output->writeln(str_pad($lineNo + 1, 6, ' ', STR_PAD_LEFT) . ': ' . $line);
                }
            }
        }
    }
}
