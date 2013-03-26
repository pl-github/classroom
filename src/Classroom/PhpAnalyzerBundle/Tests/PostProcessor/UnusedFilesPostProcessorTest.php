<?php

namespace Classroom\PhpAnalyzerBundle\PostProcessor;

use Classroom\AnalyzerBundle\Log\Log;
use Classroom\AnalyzerBundle\Result\Result;
use Classroom\AnalyzerBundle\Result\Smell\Smell;
use Classroom\AnalyzerBundle\Result\Source\Source;
use Classroom\AnalyzerBundle\Result\Source\SourceRange;
use Classroom\AnalyzerBundle\Result\Source\Storage\StringStorage;
use Classroom\PhpAnalyzerBundle\Node\PhpFileNode;
use Classroom\PhpAnalyzerBundle\PostProcessor\UnusedFilesPostProcessor;
use Classroom\PhpAnalyzerBundle\Node\PhpClassNode;

class UnusedFilesPostProcessorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @inheritDoc
     */
    public function testPostProcess()
    {
        $result = new Result();
        $result->setLog(new Log());

        $fileNode1 = new PhpFileNode('file1');
        $classNode1 = new PhpClassNode('class1');
        $source1 = new Source(new StringStorage('content1'));

        $result->addNode($fileNode1);
        $result->addNode($classNode1, $fileNode1);
        $result->addSource($source1, $fileNode1);

        $fileNode2 = new PhpFileNode('file2');
        $classNode2 = new PhpClassNode('class2');
        $smell2 = new Smell('origin', 'rule', 'text', new SourceRange(1, 2), 5);
        $source2 = new Source(new StringStorage('content2'));

        $result->addNode($fileNode2);
        $result->addNode($classNode2, $fileNode2);
        $result->addSmell($smell2, $classNode2);
        $result->addSource($source2, $fileNode2);

        $postProcessor = new UnusedFilesPostProcessor();
        $postProcessor->process($result);

        $this->assertFalse($result->hasNode('file1'));
        $this->assertTrue($result->hasNode('class1'));
        $this->assertFalse($result->hasSource($source1->getHash()));

        $this->assertTrue($result->hasNode('file2'));
        $this->assertTrue($result->hasNode('class2'));
        $this->assertTrue($result->hasSmell($smell2->getHash()));
        $this->assertTrue($result->hasSource($source2->getHash()));
    }
}
