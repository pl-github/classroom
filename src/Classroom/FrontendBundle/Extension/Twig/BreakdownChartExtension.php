<?php 

namespace Classroom\FrontendBundle\Extension\Twig;

use Classroom\ProjectBundle\Entity\Revision;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class BreakdownChartExtension extends \Twig_Extension
{
    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }
    
    public function getFilters()
    {
        return array(
            'breakdownChart' => new \Twig_Filter_Method($this, 'breakdownChart', array('is_safe' => array('html'))),
        );
    }

    public function breakdownChart(Revision $revision)
    {
        $data = array();
        $project = $revision->getProject()->getKey();
        
        foreach ($revision->getBreakdown() as $grade => $count) {
            if ($count > 0) {
                $link = $this->urlGenerator->generate(
                    'classroom_project_nodes',
                    array('grade' => $grade, 'projectKey' => $project),
                    UrlGeneratorInterface::ABSOLUTE_PATH
                );
                $data[] = array(
                    'label' => strtoupper($grade) . "-Grade",
                    'count' => $count,
                    'link'  => $link,
                );
            }
        }

        return json_encode($data);
    }

    public function getName()
    {
        return 'breakdownchart_extension';
    }
}
