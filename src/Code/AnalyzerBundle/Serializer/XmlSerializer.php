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

        foreach ($result->getReferences() as $dir => $dirReferences) {
            foreach ($dirReferences as $type => $typeReferences) {
                foreach ($typeReferences as $referenceKey => $references) {
                    $xmlReference = $dom->createElement('reference');
                    $xmlReferences->appendChild($xmlReference);

                    $xmlDir = $dom->createAttribute('dir');
                    $xmlDir->value = $dir;
                    $xmlReference->appendChild($xmlDir);

                    $xmlType = $dom->createAttribute('type');
                    $xmlType->value = $type;
                    $xmlReference->appendChild($xmlType);

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

            $xmlSource->appendChild($dom->createTextNode($source->getContent()));
        }

        return $dom->saveXML();
    }

    public function deserialize($data)
    {
        $result = new ResultModel();

        $xml = simplexml_load_string($data);

        foreach ($xml->nodes->node as $xmlNode) {
            $nodeAttr = $xmlNode->attributes();
            $name = (string)$nodeAttr['name'];
            $class = (string)$nodeAttr['class'];

            $node = new $class($name);
            $result->addNode($node);
        }

        foreach ($xml->sources->source as $xmlSource) {
            $sourceAttr = $xmlSource->attributes();
            $hash = (string)$sourceAttr['hash'];

            $storage = new StringStorage((string)$xmlSource);
            $source = new Source($storage);
            $source->setHash($hash);
            $result->addSource($source);
        }

        foreach ($xml->smells->smell as $xmlSmell) {
            $smellAttr = $xmlSmell->attributes();
            $hash = (string)$smellAttr['hash'];
            $origin = (string)$smellAttr['origin'];
            $rule = (string)$smellAttr['rule'];
            $score = (string)$smellAttr['score'];
            $beginLine = (integer)$smellAttr['beginLine'];
            $endLine = (integer)$smellAttr['endLine'];

            $sourceRange = new SourceRange($beginLine, $endLine);
            $smell = new Smell($origin, $rule, '', $sourceRange, $score);
            $smell->setHash($hash);
            $result->addSmell($smell);
        }

        foreach ($xml->references->reference as $xmlReference) {
            $referenceAttr = $xmlReference->attributes();
            $dir = (string)$referenceAttr['dir'];
            $type = (string)$referenceAttr['type'];
            $hash = (string)$referenceAttr['hash'];

            foreach ($xmlReference->referenceHash as $xmlReferenceHash) {
                $referenceHash = (string)$xmlReferenceHash;

                if ($type === 'node') {
                    $reference = $result->getNode($referenceHash);
                    if ($dir === 'incoming') {
                        $result->addMultiReference($dir, $type, $hash, $reference);
                    } else {
                        $result->addSingleReference($dir, $type, $hash, $reference);
                    }
                } elseif ($type === 'smell') {
                    if ($dir === 'incoming') {
                        $reference = $result->getSmell($referenceHash);
                        $result->addMultiReference($dir, $type, $hash, $reference);
                    } else {
                        $reference = $result->getNode($referenceHash);
                        $result->addSingleReference($dir, $type, $hash, $reference);
                    }
                } elseif ($type === 'source') {
                    if ($dir === 'incoming') {
                        $reference = $result->getSource($referenceHash);
                    } else {
                        $reference = $result->getNode($referenceHash);
                    }
                    $result->addSingleReference($dir, $type, $hash, $reference);
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
