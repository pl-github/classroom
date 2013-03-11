<?php

namespace Code\MetricsBundle\Tests\Pdepend\PdependParser;

use Code\MetricsBundle\Pdepend\PdependParser;
use org\bovigo\vfs\vfsStream;

class PdependParserTest extends \PHPUnit_Framework_TestCase
{
    public function testParse()
    {
        $pdependXml = <<<EOL
<?xml version="1.0" encoding="UTF-8"?>
<metrics generated="2013-03-11T09:08:28" pdepend="1.1.0" ahh="0.20833333333333" andc="0" calls="557" ccn="290" ccn2="300" cloc="1242" clsa="0" clsc="70" eloc="2466" fanout="189" leafs="65" lloc="1473" loc="4555" maxDIT="2" ncloc="3313" noc="70" nof="0" noi="12" nom="237" nop="44" roots="5">
  <package name="package1" cr="0.15" noc="1" nof="0" noi="0" nom="1" rcr="0.1755">
    <class name="class1" ca="0" cbo="3" ce="3" cis="1" cloc="6" cr="0.15" csz="1" dit="2" eloc="4" impl="0" lloc="2" loc="15" ncloc="9" noam="1" nocc="0" nom="1" noom="0" npm="1" rcr="0.1755" vars="0" varsi="0" varsnp="0" wmc="1" wmci="1" wmcnp="1">
      <file name="file1.php"/>
      <method name="method1" ccn="1" ccn2="1" cloc="0" eloc="4" lloc="2" loc="6" ncloc="6" npath="1"/>
    </class>
  </package>
  <package name="package2" cr="0.15" noc="1" nof="0" noi="0" nom="1" rcr="0.2775">
    <class name="class2" ca="1" cbo="2" ce="2" cis="1" cloc="0" cr="0.15" csz="1" dit="0" eloc="10" impl="1" lloc="6" loc="16" ncloc="16" noam="0" nocc="0" nom="1" noom="0" npm="1" rcr="0.2775" vars="0" varsi="0" varsnp="0" wmc="3" wmci="3" wmcnp="3">
      <file name="file2.php"/>
      <method name="method2" ccn="3" ccn2="3" cloc="0" eloc="10" lloc="6" loc="13" ncloc="13" npath="4"/>
    </class>
  </package>
</metrics>
EOL;

        vfsStream::setup('root', 0777, array('pdepend.xml' => $pdependXml));

        $parser = new PdependParser();

        $pdepend = $parser->parse(vfsStream::url('root/pdepend.xml'));

        $this->assertEquals('1.1.0', $pdepend->getPdepend());
        $this->assertEquals('2013-03-11 09:08:28', $pdepend->getGenerated()->format('Y-m-d H:i:s'));
        $this->assertEquals(21, count($pdepend->getMetrics()));

        return $pdepend;
    }

    /**
     * @depends testParse
     */
    public function testPackagesFromPdepend($pdepend)
    {
        $packages = $pdepend->getPackages();
        $this->assertEquals(2, count($packages));

        return $packages;
    }

    /**
     * @depends testPackagesFromPdepend
     */
    public function testPackage1(array $packages)
    {
        $package1 = $packages[0];

        $this->assertEquals('package1', $package1->getName());

        return $package1;
    }

    /**
     * @depends testPackage1
     */
    public function testClassesFromPackage1($package1)
    {
        $classes = $package1->getClasses();
        $this->assertEquals(1, count($classes));

        return $classes;
    }

    /**
     * @depends testClassesFromPackage1
     */
    public function testClass1($classes)
    {
        $class = $classes[0];

        $this->assertEquals('class1', $class->getName());

        return $class;
    }

    /**
     * @depends testClass1
     */
    public function testFileFromClass1($class)
    {
        $this->assertEquals('file1.php', $class->getFile());
    }

    /**
     * @depends testClass1
     */
    public function testMethodsFromClass1($class)
    {
        $methods = $class->getMethods();
        $this->assertEquals(1, count($methods));

        return $methods;
    }

    /**
     * @depends testMethodsFromClass1
     */
    public function testMethod1($methods)
    {
        $method = $methods[0];

        $this->assertEquals('method1', $method->getName());
    }

    /**
     * @depends testPackagesFromPdepend
     */
    public function testPackage2(array $packages)
    {
        $package1 = $packages[1];

        $this->assertEquals('package2', $package1->getName());

        return $package1;
    }

    /**
     * @depends testPackage2
     */
    public function testClassesFromPackage2($package2)
    {
        $classes = $package2->getClasses();
        $this->assertEquals(1, count($classes));

        return $classes;
    }

    /**
     * @depends testClassesFromPackage2
     */
    public function testClass2($classes)
    {
        $class = $classes[0];

        $this->assertEquals('class2', $class->getName());

        return $class;
    }

    /**
     * @depends testClass2
     */
    public function testFileFromClass2($class)
    {
        $this->assertEquals('file2.php', $class->getFile());
    }

    /**
     * @depends testClass2
     */
    public function testMethodsFromClass2($class)
    {
        $methods = $class->getMethods();
        $this->assertEquals(1, count($methods));

        return $methods;
    }

    /**
     * @depends testMethodsFromClass2
     */
    public function testMethod2($methods)
    {
        $method = $methods[0];

        $this->assertEquals('method2', $method->getName());
    }
}
