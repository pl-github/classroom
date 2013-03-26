<?php

namespace Code\AnalyzerBundle\PostProcessor;

use Code\AnalyzerBundle\Grader\Gradable;
use Code\AnalyzerBundle\Grader\GraderInterface;
use Code\AnalyzerBundle\Result\Result;

class GradePostProcessor implements PostProcessorInterface
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
    public function process(Result $result)
    {
        $result->getLog()->addProcess('(GradeFilter) Setting grades');

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
