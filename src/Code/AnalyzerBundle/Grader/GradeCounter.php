<?php

namespace Code\AnalyzerBundle\Grader;

use Code\AnalyzerBundle\Grader\Gradable;
use Code\AnalyzerBundle\Result\Result;

class GradeCounter
{
    /**
     * Count grades
     *
     * @param Result $result
     * @return array
     */
    public function countGrades(Result $result)
    {
        $breakdown = array('A' => 0, 'B' => 0, 'C' => 0, 'D' => 0, 'F' => 0);

        foreach ($result->getNodes() as $node) {
            if (!$node instanceof Gradable) {
                continue;
            }

            $grade = $node->getGrade();

            if (!array_key_exists($grade, $breakdown)) {
                continue;
            }

            $breakdown[$grade]++;
        }

        return $breakdown;
    }

}
