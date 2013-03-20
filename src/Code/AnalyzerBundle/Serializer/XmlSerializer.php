<?php

namespace Code\AnalyzerBundle\Serializer;

use Code\AnalyzerBundle\Model\NodeInterface;
use Code\AnalyzerBundle\Model\Reference;
use Code\AnalyzerBundle\Model\ResultModel;
use Code\AnalyzerBundle\Model\SmellModel;
use Code\AnalyzerBundle\Smell\Smell;
use Code\AnalyzerBundle\Source\Source;
use Code\AnalyzerBundle\Source\SourceRange;
use Code\AnalyzerBundle\Source\Storage\StringStorage;

class XmlSerializer implements SerializerInterface
{
    /**
     * @inheritDoc
     */
    public function serialize(ResultModel $result)
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;

        $xmlResult = $dom->appendChild($dom->createElement('result'));
        $xmlNodes = $xmlResult->appendChild($dom->createElement('nodes'));
        $xmlSmells = $xmlResult->appendChild($dom->createElement('smells'));
        $xmlSources = $xmlResult->appendChild($dom->createElement('sources'));
        $xmlReferences = $xmlResult->appendChild($dom->createElement('references'));

        foreach ($result->getNodes() as $node) {
            $xmlNode = $dom->createElement('node');
            $xmlNodes->appendChild($xmlNode);

            $xmlClass = $dom->createAttribute('class');
            $xmlClass->value = get_class($node);
            $xmlNode->appendChild($xmlClass);

            $xmlHash = $dom->createAttribute('hash');
            $xmlHash->value = $node->getHash();
            $xmlNode->appendChild($xmlHash);

            $xmlName = $dom->createAttribute('name');
            $xmlName->value = $node->getHash();
            $xmlNode->appendChild($xmlName);
        }

        foreach ($result->getSmells() as $smell) {
            $xmlSmell = $dom->createElement('smell');
            $xmlSmells->appendChild($xmlSmell);

            $xmlClass = $dom->createAttribute('class');
            $xmlClass->value = get_class($smell);
            $xmlSmell->appendChild($xmlClass);

            $xmlHash = $dom->createAttribute('hash');
            $xmlHash->value = $smell->getHash();
            $xmlSmell->appendChild($xmlHash);

            $xmlOrigin = $dom->createAttribute('origin');
            $xmlOrigin->value = $smell->getOrigin();
            $xmlSmell->appendChild($xmlOrigin);

            $xmlRule = $dom->createAttribute('rule');
            $xmlRule->value = $smell->getRule();
            $xmlSmell->appendChild($xmlRule);

            $xmlScore = $dom->createAttribute('score');
            $xmlScore->value = $smell->getScore();
            $xmlSmell->appendChild($xmlScore);

            $xmlBeginLine = $dom->createAttribute('beginLine');
            $xmlBeginLine->value = $smell->getSourceRange()->getBeginLine();
            $xmlSmell->appendChild($xmlBeginLine);

            $xmlEndLine = $dom->createAttribute('endLine');
            $xmlEndLine->value = $smell->getSourceRange()->getEndLine();
            $xmlSmell->appendChild($xmlEndLine);
        }

        foreach ($result->getReferences() as $type => $typeReferences) {
            foreach ($typeReferences as $direction => $directionReferences) {
                foreach ($directionReferences as $referenceKey => $references) {
                    $xmlReference = $dom->createElement('reference');
                    $xmlReferences->appendChild($xmlReference);

                    $xmlType = $dom->createAttribute('type');
                    $xmlType->value = $type;
                    $xmlReference->appendChild($xmlType);

                    $xmlDirection = $dom->createAttribute('direction');
                    $xmlDirection->value = $direction;
                    $xmlReference->appendChild($xmlDirection);

                    $xmlKey = $dom->createAttribute('hash');
                    $xmlKey->value = $referenceKey;
                    $xmlReference->appendChild($xmlKey);

                    if (!is_array($references)) {
                        $references = array($references);
                    }

                    foreach ($references as $reference) {
                        $xmlReferenceHash = $dom->createElement('referenceHash');
                        $xmlReferenceHash->appendChild($dom->createTextNode($reference->getReferenceHash()));
                        $xmlReference->appendChild($xmlReferenceHash);
                    }
                }
            }
        }

        foreach ($result->getSources() as $source) {
            $xmlSource = $dom->createElement('source');
            $xmlSources->appendChild($xmlSource);

            $xmlHash = $dom->createAttribute('hash');
            $xmlHash->value = $source->getHash();
            $xmlSource->appendChild($xmlHash);

            $xmlClass = $dom->createAttribute('class');
            $xmlClass->value = get_class($source);
            $xmlSource->appendChild($xmlClass);

            $xmlStorageClass = $dom->createAttribute('storageClass');
            $xmlStorageClass->value = get_class($source->getStorage());
            $xmlSource->appendChild($xmlStorageClass);

            switch (get_class($source->getStorage())) {
                case 'Code\AnalyzerBundle\Source\Storage\FilesystemStorage':
                    $xmlSource->appendChild($dom->createTextNode($source->getStorage()->getFilename()));
                    break;
                case 'Code\AnalyzerBundle\Source\Storage\StringStorage':
                    $xmlSource->appendChild($dom->createTextNode($source->getStorage()->getContent()));
                    break;
                default:
                    throw new \Exception('Unknown storage');
            }
        }

        return $dom->saveXML();
    }

    /**
     * @inheritDoc
     */
    public function deserialize($data)
    {
        $result = new ResultModel();

        $xml = simplexml_load_string($data);

        foreach ($xml->nodes->node as $xmlNode) {
            $nodeAttr = $xmlNode->attributes();
            $name = (string)$nodeAttr['name'];
            $classname = (string)$nodeAttr['class'];

            $node = new $classname($name);
            $result->addNode($node);
        }

        foreach ($xml->sources->source as $xmlSource) {
            $sourceAttr = $xmlSource->attributes();
            $hash = (string)$sourceAttr['hash'];
            $classname = (string)$sourceAttr['class'];
            $storageClassname = (string)$sourceAttr['storageClass'];

            $storage = new $storageClassname((string)$xmlSource);
            $source = new $classname($storage);
            $source->setHash($hash);
            $result->addSource($source);
        }

        foreach ($xml->smells->smell as $xmlSmell) {
            $smellAttr = $xmlSmell->attributes();
            $classname = (string)$smellAttr['class'];
            $hash = (string)$smellAttr['hash'];
            $origin = (string)$smellAttr['origin'];
            $rule = (string)$smellAttr['rule'];
            $score = (string)$smellAttr['score'];
            $beginLine = (integer)$smellAttr['beginLine'];
            $endLine = (integer)$smellAttr['endLine'];

            $sourceRange = new SourceRange($beginLine, $endLine);
            $smell = new $classname($origin, $rule, '', $sourceRange, $score);
            $smell->setHash($hash);
            $result->addSmell($smell);
        }

        foreach ($xml->references->reference as $xmlReference) {
            $referenceAttr = $xmlReference->attributes();
            $type = (string)$referenceAttr['type'];
            $direction = (string)$referenceAttr['direction'];
            $hash = (string)$referenceAttr['hash'];

            foreach ($xmlReference->referenceHash as $xmlReferenceHash) {
                $referenceHash = (string)$xmlReferenceHash;

                if ($type === 'node') {
                    $nodeReference = $result->getNode($referenceHash);
                    if ($direction === 'children') {
                        $result->addMultiReference($type, $direction, $hash, $nodeReference);
                    } elseif ($direction === 'parent') {
                        $result->addSingleReference($type, $direction, $hash, $nodeReference);
                    } else {
                        throw new \Exception('Unknown reference direction ' . $direction);
                    }
                } elseif ($type === 'smell') {
                    if ($direction === 'nodeToSmells') {
                        $smellReference = $result->getSmell($referenceHash);
                        $result->addMultiReference($type, $direction, $hash, $smellReference);
                    } elseif ($direction === 'smellToNode') {
                        $nodeReference = $result->getNode($referenceHash);
                        $result->addSingleReference($type, $direction, $hash, $nodeReference);
                    } else {
                        throw new \Exception('Unknown reference direction ' . $direction);
                    }
                } elseif ($type === 'source') {
                    if ($direction === 'nodeToSource') {
                        $sourceReference = $result->getSource($referenceHash);
                        $result->addSingleReference($type, $direction, $hash, $sourceReference);
                    } elseif ($direction === 'sourceToNode') {
                        $nodeReference = $result->getNode($referenceHash);
                        $result->addSingleReference($type, $direction, $hash, $nodeReference);
                    } else {
                        throw new \Exception('Unknown reference direction ' . $direction);
                    }

                }
            }
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function getType()
    {
        return 'xml';
    }
}
