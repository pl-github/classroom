<?php

namespace Code\AnalyzerBundle\Filter;

use Code\AnalyzerBundle\Grader\GpaCalculator;
use Code\AnalyzerBundle\Grader\GraderInterface;
use Code\AnalyzerBundle\Model\ResultModel;
use Code\AnalyzerBundle\Node\Gradable;

class GradeFilter implements FilterInterface
{
    /**
     * @var GraderInterface
     */
    private $grader;

    /**
     * @var GpaCalculator
     */
    private $gpaCalculator;

    /**
     * @param GraderInterface $grader
     * @param GpaCalculator   $gpaCalculator
     */
    public function __construct(GraderInterface $grader, GpaCalculator $gpaCalculator)
    {
        $this->grader = $grader;
        $this->gpaCalculator = $gpaCalculator;
    }

    /**
     * @inheritDoc
     */
    public function filter(ResultModel $result)
    {
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

            $grade = $this->grader->grade($node, $smells);

            $node->setGrade($grade);
        }

        $result->setGpa($this->gpaCalculator->calculate($result));
    }
}
