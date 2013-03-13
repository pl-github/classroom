<?php

namespace Code\ProjectBundle\Tests\Build\Loader;

use Code\ProjectBundle\Build\Loader\SerializeLoader;
use Code\ProjectBundle\Project;
use Code\RepositoryBundle\RepositoryConfig;
use PhpOption\Tests\Repository;
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
                        'build' => array(
                            'testVersion.serialized' => serialize('test')
                        )
                    )
                )
            )
        );

        $this->loader = new SerializeLoader(vfsStream::url('root'));
    }

    public function testLoad()
    {
        $project = new Project('testProject', 'Test Project', 'testSourceDir', new RepositoryConfig('local', 'url'));

        $result = $this->loader->load($project, 'testVersion');

        $this->assertEquals('test', $result);
    }
}
