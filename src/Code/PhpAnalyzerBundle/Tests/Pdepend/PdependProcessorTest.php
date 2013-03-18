<?php

namespace Code\MetricsBundle\Tests\Pdepend\PdependProcessor;

use Code\MetricsBundle\Pdepend\PdependProcessor;
use org\bovigo\vfs\vfsStream;
use Code\AnalyzerBundle\Model\ClassesModel;

class PdependProcessorTest extends \PHPUnit_Framework_TestCase
{
    public function testProcess()
    {
        $pdependXml = <<<EOL
<?xml version="1.0" encoding="UTF-8"?>
<metrics generated="2013-03-11T09:08:28" pdepend="1.1.0" ahh="0.20833333333333" andc="0" calls="557" ccn="290"
         ccn2="300" cloc="1242" clsa="0" clsc="70" eloc="2466" fanout="189" leafs="65" lloc="1473" loc="4555"
         maxDIT="2" ncloc="3313" noc="70" nof="0" noi="12" nom="237" nop="44" roots="5">
  <package name="package1" cr="0.15" noc="1" nof="0" noi="0" nom="1" rcr="0.1755">
    <class name="class1" ca="0" cbo="3" ce="3" cis="1" cloc="6" cr="0.15" csz="1" dit="2" eloc="4" impl="0" lloc="2"
           loc="15" ncloc="9" noam="1" nocc="0" nom="1" noom="0" npm="1" rcr="0.1755" vars="0" varsi="0" varsnp="0"
           wmc="20" wmci="1" wmcnp="1">
      <file name="file1.php"/>
      <method name="method1" ccn="1" ccn2="1" cloc="0" eloc="4" lloc="2" loc="6" ncloc="6" npath="1"/>
    </class>
  </package>
  <package name="package2" cr="0.15" noc="1" nof="0" noi="0" nom="1" rcr="0.2775">
    <class name="class2" ca="1" cbo="2" ce="2" cis="1" cloc="0" cr="0.15" csz="1" dit="0" eloc="10" impl="1" lloc="6"
           loc="16" ncloc="16" noam="0" nocc="0" nom="1" noom="0" npm="1" rcr="0.2775" vars="0" varsi="0" varsnp="0"
           wmc="3" wmci="3" wmcnp="3">
      <file name="file2.php"/>
      <method name="method2" ccn="3" ccn2="3" cloc="0" eloc="10" lloc="6" loc="13" ncloc="13" npath="4"/>
    </class>
  </package>
</metrics>
EOL;

        vfsStream::setup('root', 0777, array('pdepend.xml' => $pdependXml));

        $reflectionServiceMock = $this->getMockBuilder('Code\AnalyzerBundle\ReflectionService')
            ->disableOriginalConstructor()
            ->getMock();

        $reflectionServiceMock
            ->expects($this->any())
            ->method('getClassnameForFile')
            ->will($this->returnArgument(0));

        $processor = new PdependProcessor($reflectionServiceMock);

        $classes = $processor->process(vfsStream::url('root/pdepend.xml'));

        $this->assertEquals(2, count($classes->getClasses()));

        return $classes;
    }

    /**
     * @depends testProcess
     * @param ClassesModel $classes
     */
    public function testClass1(ClassesModel $classes)
    {
        $classes = $classes->getClasses();
        $class = $classes['package1\class1'];
        /* @var $class ClassModel */

        $this->assertEquals('class1', $class->getName());
    }

    /**
     * @depends testProcess
     * @param ClassesModel $classes
     */
    public function testClass2(ClassesModel $classes)
    {
        $classes = $classes->getClasses();
        $class = $classes['package2\class2'];
        /* @var $class ClassModel */

        $this->assertEquals('class2', $class->getName());
    }
}
