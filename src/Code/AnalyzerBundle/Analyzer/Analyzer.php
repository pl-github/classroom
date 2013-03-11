<?php

namespace Code\AnalyzerBundle\Analyzer;

use Code\AnalyzerBundle\Analyzer\Runner\RunnerInterface;
use Code\AnalyzerBundle\Analyzer\Mapper\MapperInterface;
use Code\AnalyzerBundle\Analyzer\Parser\ParserInterface;

class Analyzer implements AnalyzerInterface
{
    /**
     * @var RunnerInterface
     */
    private $runner;

    /**
     * @var ParserInterface
     */
    private $parser;

    /**
     * @var MapperInterface
     */
    private $mapper;

    /**
     * @param RunnerInterface $runner
     * @param ParserInterface $parser
     * @param MapperInterface $mapper
     */
    public function __construct(RunnerInterface $runner, ParserInterface $parser, MapperInterface $mapper)
    {
        $this->runner = $runner;
        $this->parser = $parser;
        $this->mapper = $mapper;
    }

    /**
     * @inheritDoc
     */
    public function analyze($sourceDirectory, $workDirectory)
    {
        $filename = $this->runner->run($sourceDirectory, $workDirectory);
        $result = $this->parser->parse($filename);
        $classes = $this->mapper->map($result);

        return $classes;
    }
}
