<?php

namespace Code\AnalyzerBundle\Writer;

use Code\AnalyzerBundle\Model\NodeInterface;
use Code\AnalyzerBundle\Model\ResultModel;
use Code\AnalyzerBundle\Model\SmellModel;

class PharWriter
{
    /**
     * @var string
     */
    private $dataDir;

    /**
     * @param string $rootDir
     */
    public function __construct($rootDir)
    {
        $this->dataDir = $rootDir . '/data';
    }

    /**
     * @param ResultModel $result
     */
    public function createPhar(ResultModel $result)
    {
        $phar = new \Phar($this->dataDir . '/test.phar');
        $phar->setStub(
            '<?php Phar::mapPhar("result.phar"); __HALT_COMPILER();'
        );

        $references = array();
        foreach ($result->getSmells() as $smell) {
            /* @var $smell SmellModel */

            $nodeReference = $smell->getNodeReference();
            $references[$nodeReference->getReferenceName()] = $smell->getNodeReference();
        }

        foreach ($references as $reference) {
            $node = $result->getNode($reference);
            $fileNode = $this->findRootNode($result, $node);

            $source = file_get_contents($fileNode->getFullQualifiedName());
            $sourceFilename = 'source/' . sha1($fileNode->getFullQualifiedName()) . '.sha1';
            $phar->addFromString(
                $sourceFilename,
                $source
            );

            $fileNode->setSourceFilename('phar://result.phar/' . $sourceFilename);
        }

        $phar->addFromString('result.serialized', serialize($result));

        return $phar;
    }

    private function findRootNode(ResultModel $result, NodeInterface $node)
    {
        $parentNodeReference = $node->getParentNodeReference();

        if (!$parentNodeReference) {
            return $node;
        }

        while ($parentNodeReference) {
            $node = $result->getNode($parentNodeReference);
            $parentNodeReference = $node->getParentNodeReference();
        }

        return $node;
    }
}
