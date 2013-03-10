<?php

namespace Code\MessDetectionBundle;

use Code\MessDetectionBundle\Phpmd\PhpmdExecutor;
use Code\MessDetectionBundle\Phpmd\PhpmdMapper;
use Code\MessDetectionBundle\Phpmd\PhpmdParser;
use Code\ProjectBundle\ServiceInterface;

class MessDetectionService implements ServiceInterface
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
    public function __construct(PhpmdExecutor $executor, PhpmdParser $parser, PhpmdMapper $mapper)
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
