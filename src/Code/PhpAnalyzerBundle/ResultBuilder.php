<?php

namespace Code\PhpAnalyzerBundle;

use Code\AnalyzerBundle\Analyzer\AnalyzerInterface;
use Code\AnalyzerBundle\Model\ResultModel;
use Code\AnalyzerBundle\ResultBuilderInterface;

class ResultBuilder implements ResultBuilderInterface
{
    /**
     * @inheritDoc
     */
    public function bla(AnalyzerInterface $analyzer, $sourceDirectory, $workDirectory)
    {
        $result = new ResultModel();

        $analyzer->analyze($result, $sourceDirectory, $workDirectory);

        return $result;
    }
}
