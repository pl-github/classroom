<?php

namespace Classroom\AnalyzerBundle\Grader;

use Classroom\AnalyzerBundle\Result\Metric\Measurable;
use Classroom\AnalyzerBundle\Result\Node\NodeInterface;

class Grader implements GraderInterface
{
    /**
     * @var array
     */
    private $scoreToGradeMap = array(
        'A' => 1,
        'B' => 2,
        'C' => 3,
        'D' => 5,
        'F' => null
    );

    /**
     * @inheritDoc
     */
    public function grade(NodeInterface $node, array $smells)
    {
        if (!count($smells)) {
            return 'A';
        }

        $score = $this->getScore($smells);
        $linesOfCode = $this->getLinesOfCode($node);
        $factor = $this->getFactor($linesOfCode);
        $weightedScore = $this->getWeightedScore($score, $factor);

        $grade = $this->scoreToGrade($weightedScore);

        return $grade;
    }

    /**
     * @inheritDoc
     */
    public function getCalculationValues(NodeInterface $node, array $smells)
    {
        $values = array();
        $values['score'] = $this->getScore($smells);
        $values['linesOfCode'] = $this->getLinesOfCode($node);
        $values['factor'] = $this->getFactor($values['linesOfCode']);
        $values['weightedScore'] = $this->getFactor($values['score'], $values['factor']);
        return $values;
    }

    private function getScore(array $smells)
    {
        $score = 0;
        foreach ($smells as $smell) {
            $score += $smell->getScore();
        }

        return $score;
    }

    private function getLinesOfCode(NodeInterface $node)
    {
        $linesOfCode = 0;

        if ($node instanceof Measurable && $node->hasMetric('linesOfCode')) {
            $linesOfCode = $node->getMetric('linesOfCode')->getValue();
        }

        return $linesOfCode;
    }

    private function getFactor($linesOfCode)
    {
        $factor = log($linesOfCode);

        if ($factor < 1) {
            $factor = 1;
        }

        return $factor;
    }

    private function getWeightedScore($score, $factor)
    {
        if ($factor < 1) {
            $factor = 1;
        }

        $weightedScore = $score / $factor;

        return $weightedScore;
    }

    /**
     * Get grade from score
     *
     * @param float $score
     * @return string
     */
    private function scoreToGrade($score)
    {
        if ($score <= $this->scoreToGradeMap['A']) {
            $grade = 'A';
        } elseif ($score <= $this->scoreToGradeMap['B']) {
            $grade = 'B';
        } elseif ($score <= $this->scoreToGradeMap['C']) {
            $grade = 'C';
        } elseif ($score <= $this->scoreToGradeMap['D']) {
            $grade = 'D';
        } else {
            $grade = 'F';
        }

        return $grade;
    }
}
