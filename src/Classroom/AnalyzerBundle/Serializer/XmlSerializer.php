<?php

namespace Classroom\AnalyzerBundle\Serializer;

use Classroom\AnalyzerBundle\Grader\Gradable;
use Classroom\AnalyzerBundle\Result\Metric\Measurable;
use Classroom\AnalyzerBundle\Result\Node\NodeInterface;
use Classroom\AnalyzerBundle\Result\Reference\Reference;
use Classroom\AnalyzerBundle\Result\Result;
use Classroom\AnalyzerBundle\Result\Smell\Smell;
use Classroom\AnalyzerBundle\Result\Source\Source;
use Classroom\AnalyzerBundle\Result\Source\SourceRange;
use Classroom\AnalyzerBundle\Result\Source\Storage\StringStorage;

class XmlSerializer implements SerializerInterface
{
    /**
     * @inheritDoc
     */
    public function serialize(Result $result)
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;

        $xmlResult = $dom->appendChild($dom->createElement('result'));
        $xmlNodes = $xmlResult->appendChild($dom->createElement('nodes'));
        $xmlSmells = $xmlResult->appendChild($dom->createElement('smells'));
        $xmlSources = $xmlResult->appendChild($dom->createElement('sources'));
        $xmlReferences = $xmlResult->appendChild($dom->createElement('references'));

        $xmlGpa = $dom->createAttribute('gpa');
        $xmlGpa->value = $result->getGpa();
        $xmlResult->appendChild($xmlGpa);

        $xmlBreakdown = $dom->createAttribute('breakdown');
        $breakdown = array();
        foreach ($result->getBreakdown() as $grade => $count) {
            $breakdown[] = $grade . ':' . $count;
        }
        $xmlBreakdown->value = implode(',', $breakdown);
        $xmlResult->appendChild($xmlBreakdown);

        $xmlBuiltAt = $dom->createAttribute('builtAt');
        $xmlBuiltAt->value = $result->getBuiltAt()->format('c');
        $xmlResult->appendChild($xmlBuiltAt);

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

            if ($node instanceof Gradable) {
                $xmlGrade = $dom->createAttribute('grade');
                $xmlGrade->value = $node->getGrade();
                $xmlNode->appendChild($xmlGrade);
            }

            if ($node instanceof Measurable) {
                foreach ($node->getMetrics() as $metric) {
                    $xmlMetric = $dom->createElement('metric');
                    $xmlNode->appendChild($xmlMetric);

                    $xmlMetricClass = $dom->createAttribute('class');
                    $xmlMetricClass->value = get_class($metric);
                    $xmlMetric->appendChild($xmlMetricClass);

                    $xmlMetricKey = $dom->createAttribute('key');
                    $xmlMetricKey->value = $metric->getKey();
                    $xmlMetric->appendChild($xmlMetricKey);

                    $xmlMetricValue = $dom->createAttribute('value');
                    $xmlMetricValue->value = $metric->getValue();
                    $xmlMetric->appendChild($xmlMetricValue);
                }
            }
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

            $xmlText = $dom->createTextNode($smell->getText());
            $xmlSmell->appendChild($xmlText);
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
                case 'Classroom\AnalyzerBundle\Result\Source\Storage\FilesystemStorage':
                    $xmlSource->appendChild($dom->createTextNode($source->getStorage()->getFilename()));
                    break;
                case 'Classroom\AnalyzerBundle\Result\Source\Storage\StringStorage':
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
        $result = new Result();

        $xml = simplexml_load_string($data);

        $xmlAttr = $xml->attributes();
        $gpa = (string)$xmlAttr['gpa'];
        $breakdown = (string)$xmlAttr['breakdown'];
        $builtAt = (string)$xmlAttr['builtAt'];

        $breakdownImpl = explode(',', $breakdown);
        $breakdown = array();
        foreach ($breakdownImpl as $impl) {
            list($grade, $count) = explode(':', $impl);
            $breakdown[$grade] = $count;
        }
        $result->setGpa($gpa);
        $result->setBreakdown($breakdown);
        $result->setBuiltAt(new \DateTime($builtAt));

        foreach ($xml->nodes->node as $xmlNode) {
            $nodeAttr = $xmlNode->attributes();
            $name = (string)$nodeAttr['name'];
            $classname = (string)$nodeAttr['class'];

            $node = new $classname($name);

            if ($node instanceof Gradable) {
                $grade = (string)$nodeAttr['grade'];
                $node->setGrade($grade);
            }

            if ($node instanceof Measurable) {
                foreach ($xmlNode->metric as $xmlMetric) {
                    $metricAttr = $xmlMetric->attributes();
                    $classname = (string)$metricAttr['class'];
                    $key = (string)$metricAttr['key'];
                    $value = (string)$metricAttr['value'];

                    $metric = new $classname($key, $value);
                    $node->addMetric($metric);
                }
            }

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
            $text = (string)$xmlSmell;

            $sourceRange = new SourceRange($beginLine, $endLine);
            $smell = new $classname($origin, $rule, $text, $sourceRange, $score);
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
