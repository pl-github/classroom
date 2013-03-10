<?php

namespace Code\ProjectBundle\Controller;

use Code\ProjectBundle\Build\Loader\SerializeLoader;
use Code\ProjectBundle\Project;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class IndexController extends Controller
{
    /**
     * @Route("/", name="code_index")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $card = $request->query->get('card');

        $rootDir = $this->container->getParameter('kernel.root_dir');

        $loader = $this->container->get('code.project.build.loader');
        /* @var $reader SerializeLoader */

        $project = new Project(1, 'code');
        $version = (integer)file_get_contents($rootDir . '/data/' . $project->getId() . '.version');
        $build = $loader->load($project, $version);

        return array('card' => $card, 'project' => $project, 'build' => $build);
    }

    /**
     * @Route("/stream", name="code_stream")
     * @Template()
     */
    public function streamAction()
    {
        return array();
    }

    /**
     * @Route("/smells", name="code_smells")
     * @Template()
     */
    public function smellsAction()
    {
        $rootDir = $this->container->getParameter('kernel.root_dir');

        $loader = $this->container->get('code.project.build.loader');
        /* @var $reader SerializeLoader */

        $project = new Project(1, 'code');
        $version = (integer)file_get_contents($rootDir . '/data/' . $project->getId() . '.version');
        $build = $loader->load($project, $version);
        $classes = $build->getClasses();

        return array('build' => $build, 'classes' => $classes);
    }

    /**
     * @Route("/classes", name="code_classes")
     * @Template()
     */
    public function classesAction()
    {
        $rootDir = $this->container->getParameter('kernel.root_dir');

        $loader = $this->container->get('code.project.build.loader');
        /* @var $reader SerializeLoader */

        $project = new Project(1, 'code');
        $version = (integer)file_get_contents($rootDir . '/data/' . $project->getId() . '.version');
        $classes = $loader->load($project, $version)->getClasses();

        return array('classes' => $classes);
    }
}
