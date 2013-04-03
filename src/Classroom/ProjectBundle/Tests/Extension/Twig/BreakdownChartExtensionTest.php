<?php

namespace Classroom\ProjectBundle\Tests\Extension\Twig;

use Classroom\ProjectBundle\Entity\Revision;
use Classroom\ProjectBundle\Entity\Project;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Classroom\ProjectBundle\Extension\Twig\BreakdownChartExtension;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

class BreakdownChartExtensionTest extends \PHPUnit_Framework_TestCase
{

    /**
     *  @var UrlGeneratorInterface|MockObject
     */
    protected $urlGeneratorMock;
   
    /**
     *  @var Revision|MockObject
     */
    protected $revisionMock;
    
    /**
     *  @var Project|MockObject
     */
    protected $projectMock;
   
    public function setUp()
    {
        $this->urlGeneratorMock = $this->getMockBuilder('\Symfony\Component\Routing\Generator\UrlGeneratorInterface')
            ->disableOriginalConstructor()
            ->getMock();
            
        $this->revisionMock = $this->getMockBuilder('\Classroom\ProjectBundle\Entity\Revision')
            ->disableOriginalConstructor()
            ->getMock();
            
        $this->projectMock = $this->getMockBuilder('\Classroom\ProjectBundle\Entity\Project')
            ->disableOriginalConstructor()
            ->getMock();
            
        $this->revisionMock
            ->expects($this->any())
            ->method('getProject')
            ->will($this->returnValue($this->projectMock));
    }
    
    public function testConvertigBreakdownIntoChartData()
    {
        $extension = new BreakdownChartExtension($this->urlGeneratorMock);
        
        $breakdown = array(
            "A" => 118,
            "B" => 8,
            "C" => 6,
            "D" => 3
        );
        
        $expectedRoute = 'classroom_project_nodes';
        
        $expectedResult = '['
            . '{"label":"A-Grade","count":118,"link":"\/my\/link?grade=A"},'
            . '{"label":"B-Grade","count":8,"link":"\/my\/link?grade=B"},'
            . '{"label":"C-Grade","count":6,"link":"\/my\/link?grade=C"},'
            . '{"label":"D-Grade","count":3,"link":"\/my\/link?grade=D"}'
            . ']';
            
        $this->projectMock
            ->expects($this->once())
            ->method('getKey')
            ->will($this->returnValue('test'));
            
        $this->urlGeneratorMock
            ->expects($this->at(0))
            ->method('generate')
            ->with($expectedRoute, array('projectKey' => 'test', 'grade' => 'A'), UrlGeneratorInterface::ABSOLUTE_PATH)
            ->will($this->returnValue('/my/link?grade=A'));
            
        $this->urlGeneratorMock
            ->expects($this->at(1))
            ->method('generate')
            ->with($expectedRoute, array('projectKey' => 'test', 'grade' => 'B'), UrlGeneratorInterface::ABSOLUTE_PATH)
            ->will($this->returnValue('/my/link?grade=B'));
            
        $this->urlGeneratorMock
            ->expects($this->at(2))
            ->method('generate')
            ->with($expectedRoute, array('projectKey' => 'test', 'grade' => 'C'), UrlGeneratorInterface::ABSOLUTE_PATH)
            ->will($this->returnValue('/my/link?grade=C'));
            
        $this->urlGeneratorMock
            ->expects($this->at(3))
            ->method('generate')
            ->with($expectedRoute, array('projectKey' => 'test', 'grade' => 'D'), UrlGeneratorInterface::ABSOLUTE_PATH)
            ->will($this->returnValue('/my/link?grade=D'));
            
        $this->revisionMock
            ->expects($this->once())
            ->method('getBreakdown')
            ->will($this->returnValue($breakdown));
            
        $actualResult = $extension->breakdownChart($this->revisionMock);
        
        $this->assertEquals($actualResult, $expectedResult);
    }
}
