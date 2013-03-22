<?php

namespace Code\PhpAnalyzerBundle\Filter;

use Code\AnalyzerBundle\Filter\FilterInterface;
use Code\AnalyzerBundle\Model\ResultModel;
use Code\PhpAnalyzerBundle\Node\PhpClassNode;

class UnusedFilesFilter implements FilterInterface
{
    /**
     * @inheritDoc
     */
    public function filter(ResultModel $result)
    {
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
