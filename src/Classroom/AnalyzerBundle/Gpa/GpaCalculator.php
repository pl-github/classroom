<?php

namespace Classroom\AnalyzerBundle\Gpa;

use Classroom\AnalyzerBundle\Result\Result;

class GpaCalculator
{
    /**
     * @var array
     */
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
     * @param array $breakdown
     * @return float
     */
    public function calculateGpaFromBreakdown(array $breakdown)
    {
        $nodeCount = array_sum($breakdown);
        $gpaScore = $this->breakdownToGpaScore($breakdown);

        return $gpaScore / $nodeCount;
    }

    /**
     * @param array $breakdown
     * @return float
     */
    private function breakdownToGpaScore(array $breakdown)
    {
        $score = 0;
        foreach ($breakdown as $grade => $count) {
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
    private function gradeToGpaScore($grade)
    {
        return $this->gradeToGpaScoreMap[$grade];
    }
}
