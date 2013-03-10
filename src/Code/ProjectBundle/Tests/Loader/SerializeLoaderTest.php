<?php

namespace Code\ProjectBundle\Tests\Loader;

use Code\ProjectBundle\Loader\SerializeLoader;
use org\bovigo\vfs\vfsStream;

class SerializeLoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SerializeLoader
     */
    private $loader;

    public function setUp()
    {
        vfsStream::setup(
            'root',
            0777,
            array(
                'data' => array(
                    'testProject' => array(
                        'project.serialized' => serialize('test')
                    )
                )
            )
        );

        $this->loader = new SerializeLoader(vfsStream::url('root'));
    }

    public function testLoad()
    {
        $result = $this->loader->load('testProject');

        $this->assertEquals('test', $result);
    }
}
