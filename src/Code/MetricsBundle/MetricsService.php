<?php

namespace Code\MetricsBundle;

use Code\MetricsBundle\Pdepend\PdependExecutor;
use Code\MetricsBundle\Pdepend\PdependMapper;
use Code\MetricsBundle\Pdepend\PdependParser;
use Code\ProjectBundle\ServiceInterface;

class MetricsService implements ServiceInterface
{
    /**
     * @var PdependExecutor
     */
    private $executor;

    /**
     * @var PdependParser
     */
    private $parser;

    /**
     * @var PdependMapper
     */
    private $mapper;

    /**
     * @param PdependExecutor $executor
     * @param PdependParser   $parser
     * @param PdependMapper   $mapper
     */
    public function __construct(PdependExecutor $executor, PdependParser $parser, PdependMapper $mapper)
    {
        $this->executor = $executor;
        $this->parser = $parser;
        $this->mapper = $mapper;
    }

    /**
     * @inheritDoc
     */
    public function run($directory, $workDirectory)
    {
        $pmdFilename = $this->executor->execute($directory, $workDirectory);
        $dupes = $this->parser->parse($pmdFilename);
        $result = $this->mapper->map($dupes);

        return $result;
    }
}
