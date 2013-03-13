<?php

namespace Code\AnalyzerBundle\Tests;

use Code\AnalyzerBundle\ReflectionService;
use TokenReflection\Broker;

class ReflectionServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ReflectionService
     */
    private $service;

    /**
     * @var Broker
     */
    private $brokerMock;

    public function setUp()
    {
        $this->brokerMock = $this->getMockBuilder('\TokenReflection\Broker')
            ->disableOriginalConstructor()
            ->getMock();

        $this->service = new ReflectionService($this->brokerMock);
    }

    public function tearDown()
    {
        $this->service = null;
        $this->brokerMock = null;
    }

    private function applyFileMock($brokerMock)
    {
        $fileMock = $this->getMockBuilder('\TokenReflection\ReflectionFile')
            ->disableOriginalConstructor()
            ->getMock();

        $brokerMock
            ->expects($this->once())
            ->method('getFile')
            ->will($this->returnValue($fileMock));

        return $fileMock;
    }

    private function applyNamespaceMock($brokerMock)
    {
        $fileMock = $this->applyFileMock($brokerMock);

        $namespaceMock = $this->getMockBuilder('\TokenReflection\ReflectionNamespace')
            ->disableOriginalConstructor()
            ->getMock();

        $fileMock
            ->expects($this->once())
            ->method('getNamespaces')
            ->will($this->returnValue(array($namespaceMock)));

        return $namespaceMock;
    }

    private function applyClassMock($brokerMock)
    {
        $namespaceMock = $this->applyNamespaceMock($brokerMock);

        $classMock = $this->getMockBuilder('\TokenReflection\IReflectionClass')
            ->getMock();

        $namespaceMock
            ->expects($this->once())
            ->method('getClasses')
            ->will($this->returnValue(array($classMock)));

        $classMock
            ->expects($this->once())
            ->method('getShortName')
            ->will($this->returnValue('testClass'));
    }

    public function testGetClassNameForFileUsesReflectionLookupClassnameFromFile()
    {
        $this->applyClassMock($this->brokerMock);

        $result = $this->service->getClassNameForFile('testFile.php');

        $this->assertEquals('testClass', $result);
    }

    public function testGetClassNameForFileCachesFile()
    {
        $this->applyClassMock($this->brokerMock);

        $this->service->getClassNameForFile('testFile.php');
        $result = $this->service->getClassNameForFile('testFile.php');

        $this->assertEquals('testClass', $result);
    }

    public function testGetClassnameForFileReturnsNullForFileWithoutClass()
    {
        $namespaceMock = $this->applyNamespaceMock($this->brokerMock);
        $namespaceMock
            ->expects($this->once())
            ->method('getClasses')
            ->will($this->returnValue(array()));

        $result = $this->service->getClassNameForFile('testFileWithoutClass.php');

        $this->assertNull($result);
    }

    public function testGetNamespaceNameForFileUsesReflectionLookupClassnameFromFile()
    {
        $namespaceMock = $this->applyNamespaceMock($this->brokerMock);
        $namespaceMock
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('testNamespace'));

        $result = $this->service->getNamespaceNameForFile('testFile.php');

        $this->assertEquals('testNamespace', $result);
    }

    public function testGetNamespaceNameForFileCachesFile()
    {
        $namespaceMock = $this->applyNamespaceMock($this->brokerMock);
        $namespaceMock
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('testNamespace'));

        $this->service->getNamespaceNameForFile('testFile.php');
        $result = $this->service->getNamespaceNameForFile('testFile.php');

        $this->assertEquals('testNamespace', $result);
    }

    public function testGetNamespaceNameForFileReturnsNullForFileWithoutClass()
    {
        $fileMock = $this->applyFileMock($this->brokerMock);
        $fileMock
            ->expects($this->once())
            ->method('getNamespaces')
            ->will($this->returnValue(array()));

        $result = $this->service->getNamespaceNameForFile('testFileWithoutClass.php');

        $this->assertNull($result);
    }

    public function testGetClassSource()
    {
        $classMock = $this->getMockBuilder('\TokenReflection\ReflectionClass')
            ->disableOriginalConstructor()
            ->getMock();

        $classMock
            ->expects($this->once())
            ->method('getSource')
            ->will($this->returnValue('testClassSource'));

        $this->brokerMock
            ->expects($this->once())
            ->method('getClass')
            ->will($this->returnValue($classMock));

        $result = $this->service->getClassSource('testFile.php', 'testClass');

        $this->assertEquals('testClassSource', $result);
    }

    public function testGetMethodSource()
    {
        $methodMock = $this->getMockBuilder('\TokenReflection\ReflectionMethod')
            ->disableOriginalConstructor()
            ->getMock();

        $methodMock
            ->expects($this->once())
            ->method('getSource')
            ->will($this->returnValue('testMethodSource'));

        $classMock = $this->getMockBuilder('\TokenReflection\ReflectionClass')
            ->disableOriginalConstructor()
            ->getMock();

        $classMock
            ->expects($this->once())
            ->method('getMethod')
            ->will($this->returnValue($methodMock));

        $this->brokerMock
            ->expects($this->once())
            ->method('getClass')
            ->will($this->returnValue($classMock));

        $result = $this->service->getMethodSource('testFile.php', 'testClass', 'testMethod');

        $this->assertEquals('testMethodSource', $result);
    }

    public function testGetSourceExtract()
    {
        $fileMock = $this->applyFileMock($this->brokerMock);
        $fileMock->expects($this->once())
            ->method('getSource')
            ->will($this->returnValue('A'.PHP_EOL.'B'.PHP_EOL.'C'.PHP_EOL.'D'.PHP_EOL));
        
        $result = $this->service->getSourceExtract('testFile.php', 2, 3);

        $this->assertEquals('B'.PHP_EOL.'C', $result);
    }
}
