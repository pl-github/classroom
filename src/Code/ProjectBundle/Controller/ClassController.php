<?php

namespace Code\ProjectBundle\Controller;

use Code\ProjectBundle\Build\Loader\SerializeLoader;
use Code\ProjectBundle\Project;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/class/{className}")
 */
class ClassController extends Controller
{
    private function getBuild()
    {
        $rootDir = $this->container->getParameter('kernel.root_dir');

        $loader = $this->container->get('code.project.build.loader');
        /* @var $reader SerializeLoader */

        $project = new Project(1, 'code');
        $version = (integer)file_get_contents($rootDir . '/data/' . $project->getId() . '.version');
        $build = $loader->load($project, $version);

        return $build;
    }

    /**
     * @Route("", name="code_class")
     * @Template()
     */
    public function classAction($className)
    {
        $build = $this->getBuild();
        $classes = $build->getClasses();
        $class = $classes->getClass($className);

        return array('class' => $class);
    }

    /**
     * @Route("/metrics", name="code_class_metrics")
     * @Template()
     */
    public function metricsAction($className)
    {
        $build = $this->getBuild();
        $classes = $build->getClasses();
        $class = $classes->getClass($className);
        $metrics = $class->getMetrics();

        return array('metrics' => $metrics);
    }

    /**
     * @Route("/smells", name="code_class_smells")
     * @Template()
     */
    public function smellsAction($className)
    {
        $build = $this->getBuild();
        $classes = $build->getClasses();
        $class = $classes->getClass($className);
        $smells = $class->getSmells();

        return array('smells' => $smells);
    }

    /**
     * @Route("/duplications", name="code_project_duplications")
     * @Template()
     */
    public function duplicationsAction($className)
    {
        $build = $this->getBuild();
        $classes = $build->getClasses();
        $class = $classes->getClass($className);

        return array('class' => $class);
    }

    /**
     * @Route("/messes", name="code_project_messes")
     * @Template()
     */
    public function messesAction($className)
    {
        $build = $this->getBuild();
        $classes = $build->getClasses();
        $class = $classes->getClass($className);

        return array('class' => $class);
    }

}
