<?php

namespace Code\AnalyzerBundle\Filter;

use Code\AnalyzerBundle\Model\ResultModel;
use Code\AnalyzerBundle\Node\Gradable;

class GradeFilter implements FilterInterface
{
    /**
     * @inheritDoc
     */
    public function filter(ResultModel $result)
    {
        echo PHP_EOL;
        echo str_pad('CLASS', 80) .
            str_pad('SMLLS', 6, ' ', STR_PAD_LEFT) .
            str_pad('SCORE', 6, ' ', STR_PAD_LEFT) .
            str_pad('LOC', 6, ' ', STR_PAD_LEFT) .
            str_pad('FACTOR', 18, ' ', STR_PAD_LEFT) .
            str_pad('WEIGHTED SCORE', 18, ' ', STR_PAD_LEFT) .
            str_pad('GRADE', 6, ' ', STR_PAD_LEFT) .
            PHP_EOL;

        foreach ($result->getNodes() as $node) {
            if (!$result->hasReference('smell', 'nodeToSmells', $node)) {
                continue;
            }

            if (!$node instanceof Gradable) {
                continue;
            }

            $smellReferences = $result->getReference('smell', 'nodeToSmells', $node);

            $score = 0;
            foreach ($smellReferences as $smellReference) {
                $smell = $result->getSmell($smellReference);

                $score += $smell->getScore();
            }

            $linesOfCode = 0;
            $factor = 0;
            $weightedScore = 0;
            if ($node->hasMetric('linesOfCode')) {
                $linesOfCode = $node->getMetric('linesOfCode')->getValue();
                $factor = log($linesOfCode);

                $weightedScore = $score / $factor;

                if ($weightedScore < 1) {
                    $grade = 'A';
                } elseif ($weightedScore < 2) {
                    $grade = 'B';
                } elseif ($weightedScore < 3) {
                    $grade = 'C';
                } elseif ($weightedScore < 5) {
                    $grade = 'D';
                } else {
                    $grade = 'F';
                }
            } else {
                if ($score <= 1) {
                    $grade = 'A';
                } elseif ($score <= 2) {
                    $grade = 'B';
                } elseif ($score <= 3) {
                    $grade = 'C';
                } elseif ($score <= 5) {
                    $grade = 'D';
                } else {
                    $grade = 'F';
                }
            }

            $node->setGrade($grade);

            echo str_pad($node->getName(), 80) .
                str_pad(count($smellReferences), 6, ' ', STR_PAD_LEFT) .
                str_pad($score, 6, ' ', STR_PAD_LEFT) .
                str_pad($linesOfCode, 6, ' ', STR_PAD_LEFT) .
                str_pad($factor, 18, ' ', STR_PAD_LEFT) .
                str_pad($weightedScore, 18, ' ', STR_PAD_LEFT) .
                str_pad($grade, 6, ' ', STR_PAD_LEFT) .
                PHP_EOL;
        }
    }
}