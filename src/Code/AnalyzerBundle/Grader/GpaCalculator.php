<?php

namespace Code\AnalyzerBundle\Grader;

use Code\AnalyzerBundle\Metric\Measurable;
use Code\AnalyzerBundle\Model\ResultModel;
use Code\AnalyzerBundle\Node\Gradable;

class GpaCalculator
{
    private $gradeToGpaScoreMap = array(
        'A' => 4,
        'B' => 3,
        'C' => 2,
        'D' => 1,
        'F' => 0
    );

    /**
     * Calculate GPA
     *
     * @param ResultModel $result
     * @return float
     */
    public function calculate(ResultModel $result)
    {
        $map = $this->buildGradeMap($result);
        $count = array_sum($map);
        $gpaScore = $this->mapToGpaScore($map);

        return $gpaScore / $count;
    }

    public function buildGradeMap(ResultModel $result)
    {
        $map = array('A' => 0, 'B' => 0, 'C' => 0, 'D' => 0, 'F' => 0);

        foreach ($result->getNodes() as $node) {
            if (!$node instanceof Gradable) {
                continue;
            }

            $map[$node->getGrade()]++;
        }

        return $map;
    }

    public function mapToGpaScore(array $map)
    {
        $score = 0;
        foreach ($map as $grade => $count) {
            $score += $this->gradeToGpaScore($grade) * $count;
        }
        return $score;
    }

    /**
     * Get gpa score from grade
     *
     * @param string $grade
     * @return float
     */
    public function gradeToGpaScore($grade)
    {
        return $this->gradeToGpaScoreMap[$grade];
    }
}
