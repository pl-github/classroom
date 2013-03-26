<?php

namespace Classroom\PhpAnalyzerBundle\PostProcessor;

use Classroom\AnalyzerBundle\Result\Result;
use Classroom\AnalyzerBundle\PostProcessor\PostProcessorInterface;
use Classroom\PhpAnalyzerBundle\Node\PhpClassNode;

class UnusedFilesPostProcessor implements PostProcessorInterface
{
    /**
     * @inheritDoc
     */
    public function process(Result $result)
    {
        $result->getLog()->addProcess('(UnusedFilesPostProcessor) Remove unused files and source');

        $references = $result->getReferences();

        foreach ($result->getNodes() as $node) {
            $hash = $node->getHash();

            if ($node instanceof PhpClassNode) {
                if (!array_key_exists($hash, $references['smell']['nodeToSmells'])) {
                    #echo 'class with no smell: ' . $hash . PHP_EOL;

                    $fileReference = $result->getReference('node', 'parent', $hash);
                    $fileNode = $result->getNode($fileReference);

                    $sourceReference = $result->getReference('source', 'nodeToSource', $fileReference);
                    $source = $result->getSource($sourceReference);

                    #echo 'remove file node: ' . $fileNode->getHash().PHP_EOL;
                    $result->removeNode($fileNode);

                    #echo 'remove source: ' . $source->getHash().PHP_EOL;
                    $result->removeSource($source);
                }
            }
        }
    }
}
