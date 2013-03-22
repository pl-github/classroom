<?php

namespace Code\AnalyzerBundle\Filter;

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
     * @param GraderInterface $grader
     */
    public function __construct(GraderInterface $grader)
    {
        $this->grader = $grader;
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
    }
}
