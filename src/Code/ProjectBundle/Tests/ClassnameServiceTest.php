<?php

namespace Code\ProjectBundle\Tests;

use Code\ProjectBundle\ClassnameService;
use TokenReflection\Broker;

class ClassnameServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ClassnameService
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

        $this->service = new ClassnameService($this->brokerMock);
    }

    public function tearDown()
    {
        $this->service = null;
        $this->brokerMock = null;
    }

    public function testGetClassnameForFileUsesReflectionLookupClassnameFromFile()
    {
        $classMock = $this->getMockBuilder('\TokenReflection\IReflectionClass')
            ->getMock();

        $classMock
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('testClass'));

        $this->brokerMock
            ->expects($this->once())
            ->method('getClasses')
            ->will($this->returnValue(array($classMock)));

        $result = $this->service->getClassnameForFile('testFile.php');

        $this->assertEquals('testClass', $result);
    }

    public function testGetClassnameForFileCachesFile()
    {
        $classMock = $this->getMockBuilder('\TokenReflection\IReflectionClass')
            ->getMock();

        $classMock
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('testClass'));

        $this->brokerMock
            ->expects($this->once())
            ->method('getClasses')
            ->will($this->returnValue(array($classMock)));

        $this->service->getClassnameForFile('testFile.php');
        $result = $this->service->getClassnameForFile('testFile.php');

        $this->assertEquals('testClass', $result);
    }

    public function testGetClassnameForFileReturnsNullForFileWithoutClass()
    {
        $this->brokerMock
            ->expects($this->once())
            ->method('getClasses')
            ->will($this->returnValue(array()));

        $result = $this->service->getClassnameForFile('testFileWithoutClass.php');

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
