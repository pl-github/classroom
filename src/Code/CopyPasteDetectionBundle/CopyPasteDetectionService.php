<?php

namespace Code\CopyPasteDetectionBundle;

use Code\CopyPasteDetectionBundle\Phpcpd\PhpcpdExecutor;
use Code\CopyPasteDetectionBundle\Phpcpd\PhpcpdMapper;
use Code\CopyPasteDetectionBundle\Phpcpd\PhpcpdParser;
use Code\CopyPasteDetectionBundle\Rater\CopyPasteDetectionRater;
use Code\ProjectBundle\ServiceInterface;

class CopyPasteDetectionService implements ServiceInterface
{
    /**
     * @var PhpcpdExecutor
     */
    private $executor;

    /**
     * @var PhpcpdParser
     */
    private $parser;

    /**
     * @var PhpcpdMapper
     */
    private $mapper;

    /**
     * @param PhpcpdExecutor          $executor
     * @param PhpcpdParser            $parser
     * @param PhpcpdMapper            $mapper
     * @param CopyPasteDetectionRater $rater
     */
    public function __construct(PhpcpdExecutor $executor, PhpcpdParser $parser, PhpcpdMapper $mapper)
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
        $classes = $this->mapper->map($dupes);

        return $classes;
    }
}
