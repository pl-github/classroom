<?php

namespace Classroom\PhpAnalyzerBundle\Tests;

use Classroom\PhpAnalyzerBundle\ReflectionService;
use org\bovigo\vfs\vfsStream;

class ReflectionServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ReflectionService
     */
    private $service;

    public function setUp()
    {
        $this->service = new ReflectionService();
    }

    public function tearDown()
    {
        $this->service = null;
    }

    public function testGetClassNameForFileFromSelf()
    {
        $result = $this->service->getClassNameForFile(__FILE__);

        $this->assertEquals(__CLASS__, $result);
    }

    public function testGetClassNameForFileClass()
    {
        vfsStream::setup('root', 0777, array('test.php' => '<?php class A_B_C_D {}'));

        $result = $this->service->getClassNameForFile(vfsStream::url('root/test.php'));

        $this->assertEquals('A_B_C_D', $result);
    }

    public function testGetClassNameForFileInterface()
    {
        vfsStream::setup('root', 0777, array('test.php' => '<?php interface A_B_C_D {}'));

        $result = $this->service->getClassNameForFile(vfsStream::url('root/test.php'));

        $this->assertEquals('A_B_C_D', $result);
    }

    public function testGetClassNameForFileTrait()
    {
        if (version_compare(PHP_VERSION, '5.4.0', '<')) {
            $this->markTestSkipped('Traits are not available in PHP ' . PHP_VERSION);
        }

        vfsStream::setup('root', 0777, array('test.php' => '<?php trait A_B_C_D {}'));

        $result = $this->service->getClassNameForFile(vfsStream::url('root/test.php'));

        $this->assertEquals('A_B_C_D', $result);
    }

    public function testGetClassNameForFileNamespacedClass()
    {
        vfsStream::setup('root', 0777, array('test.php' => '<?php namespace A\\B\\C; class D {}'));

        $result = $this->service->getClassNameForFile(vfsStream::url('root/test.php'));

        $this->assertEquals('A\\B\\C\\D', $result);
    }

    public function testGetClassNameForFileNamespacedInterface()
    {
        vfsStream::setup('root', 0777, array('test1.php' => '<?php namespace A\\B\\C; interface D {}'));

        $result = $this->service->getClassNameForFile(vfsStream::url('root/test1.php'));

        $this->assertEquals('A\\B\\C\\D', $result);
    }

    public function testGetClassNameForFileNamespacedTrait()
    {
        if (version_compare(PHP_VERSION, '5.4.0', '<')) {
            $this->markTestSkipped('Traits are not available in PHP ' . PHP_VERSION);
        }

        vfsStream::setup('root', 0777, array('test1.php' => '<?php namespace A\\B\\C; trait D {}'));

        $result = $this->service->getClassNameForFile(vfsStream::url('root/test1.php'));

        $this->assertEquals('A\\B\\C\\D', $result);
    }
}
