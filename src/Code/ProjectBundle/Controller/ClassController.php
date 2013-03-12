<?php

namespace Code\ProjectBundle\Controller;

use Code\ProjectBundle\Build\Loader\LoaderInterface as BuildLoaderInterface;
use Code\ProjectBundle\Project;
use Code\ProjectBundle\Loader\LoaderInterface as ProjectLoaderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/project/{projectId}/class/{className}")
 */
class ClassController extends Controller
{
    private function getLatestBuild($projectId)
    {
        $projectLoader = $this->container->get('code.project.loader');
        /* @var $projectLoader ProjectLoaderInterface */

        $buildLoader = $this->container->get('code.project.build.loader');
        /* @var $buildLoader SerializeBuildLoaderInterfaceLoader */

        $project = $projectLoader->load($projectId);
        $build = $buildLoader->load($project, $project->getLatestBuildVersion());

        return $build;
    }

    /**
     * @Route("", name="code_class")
     * @Template()
     */
    public function indexAction($projectId, $className)
    {
        $build = $this->getLatestBuild($projectId);
        $classes = $build->getClasses();
        $class = $classes->getClass($className);

        return array('project' => $build->getProject(), 'class' => $class);
    }

    /**
     * @Route("/metrics", name="code_class_metrics")
     * @Template()
     */
    public function metricsAction($projectId, $className)
    {
        $build = $this->getLatestBuild($projectId);
        $classes = $build->getClasses();
        $class = $classes->getClass($className);
        $metrics = $class->getMetrics();

        return array('metrics' => $metrics);
    }

    /**
     * @Route("/smells", name="code_class_smells")
     * @Template()
     */
    public function smellsAction($projectId, $className)
    {
        $build = $this->getLatestBuild($projectId);
        $classes = $build->getClasses();
        $class = $classes->getClass($className);
        $smells = $class->getSmells();

        return array('smells' => $smells);
    }

    /**
     * @Route("/duplications", name="code_project_duplications")
     * @Template()
     */
    public function duplicationsAction($projectId, $className)
    {
        $build = $this->getLatestBuild($projectId);
        $classes = $build->getClasses();
        $class = $classes->getClass($className);

        return array('class' => $class);
    }

    /**
     * @Route("/messes", name="code_project_messes")
     * @Template()
     */
    public function messesAction($projectId, $className)
    {
        $build = $this->getLatestBuild($projectId);
        $classes = $build->getClasses();
        $class = $classes->getClass($className);

        return array('class' => $class);
    }
}
