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

    public function testGetClassNameForFileUsesReflectionLookupClassnameFromFile()
    {
        $classMock = $this->getMockBuilder('\TokenReflection\IReflectionClass')
            ->getMock();

        $classMock
            ->expects($this->once())
            ->method('getShortName')
            ->will($this->returnValue('testClass'));

        $this->brokerMock
            ->expects($this->once())
            ->method('getClasses')
            ->will($this->returnValue(array($classMock)));

        $result = $this->service->getClassNameForFile('testFile.php');

        $this->assertEquals('testClass', $result);
    }

    public function testGetClassNameForFileCachesFile()
    {
        $classMock = $this->getMockBuilder('\TokenReflection\IReflectionClass')
            ->getMock();

        $classMock
            ->expects($this->once())
            ->method('getShortName')
            ->will($this->returnValue('testClass'));

        $this->brokerMock
            ->expects($this->once())
            ->method('getClasses')
            ->will($this->returnValue(array($classMock)));

        $this->service->getClassNameForFile('testFile.php');
        $result = $this->service->getClassNameForFile('testFile.php');

        $this->assertEquals('testClass', $result);
    }

    public function testGetClassnameForFileReturnsNullForFileWithoutClass()
    {
        $this->brokerMock
            ->expects($this->once())
            ->method('getClasses')
            ->will($this->returnValue(array()));

        $result = $this->service->getClassNameForFile('testFileWithoutClass.php');

        $this->assertNull($result);
    }

    public function testGetNamespaceNameForFileUsesReflectionLookupClassnameFromFile()
    {
        $classMock = $this->getMockBuilder('\TokenReflection\IReflectionClass')
            ->getMock();

        $classMock
            ->expects($this->once())
            ->method('getNamespaceName')
            ->will($this->returnValue('testNamespace'));

        $this->brokerMock
            ->expects($this->once())
            ->method('getClasses')
            ->will($this->returnValue(array($classMock)));

        $result = $this->service->getNamespaceNameForFile('testFile.php');

        $this->assertEquals('testNamespace', $result);
    }

    public function testGetNamespaceNameForFileCachesFile()
    {
        $classMock = $this->getMockBuilder('\TokenReflection\IReflectionClass')
            ->getMock();

        $classMock
            ->expects($this->once())
            ->method('getNamespaceName')
            ->will($this->returnValue('testNamespace'));

        $this->brokerMock
            ->expects($this->once())
            ->method('getClasses')
            ->will($this->returnValue(array($classMock)));

        $this->service->getNamespaceNameForFile('testFile.php');
        $result = $this->service->getNamespaceNameForFile('testFile.php');

        $this->assertEquals('testNamespace', $result);
    }

    public function testGetNamespaceNameForFileReturnsNullForFileWithoutClass()
    {
        $this->brokerMock
            ->expects($this->once())
            ->method('getClasses')
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
}
