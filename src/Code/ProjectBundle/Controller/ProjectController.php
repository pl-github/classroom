<?php

namespace Code\ProjectBundle\Controller;

use Code\ProjectBundle\Build\Loader\LoaderInterface as BuilderLoaderInterface;
use Code\ProjectBundle\Project;
use Code\ProjectBundle\Loader\LoaderInterface as ProjectLoaderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/project/{projectId}")
 */
class ProjectController extends Controller
{
    private function getProject($projectId)
    {
        $projectLoader = $this->container->get('code.project.loader');
        /* @var $projectLoader ProjectLoaderInterface */

        $project = $projectLoader->load($projectId);

        return $project;
    }

    private function getLatestBuild(Project $project)
    {
        $buildLoader = $this->container->get('code.project.build.loader');
        /* @var $buildLoader BuilderLoaderInterface */

        if (!$project->getLatestBuildVersion()) {
            return null;
        }

        $build = $buildLoader->load($project, $project->getLatestBuildVersion());

        return $build;
    }

    /**
     * @Route("", name="code_project")
     * @Template()
     */
    public function indexAction(Request $request, $projectId)
    {
        $card = $request->query->get('card');

        $project = $this->getProject($projectId);
        $build = $this->getLatestBuild($project);

        return array('card' => $card, 'project' => $project, 'build' => $build);
    }

    /**
     * @Route("/feed", name="code_project_feed")
     * @Template()
     */
    public function feedAction($projectId)
    {
        $project = $this->getProject($projectId);
        $feed = $project->getFeed();

        return array('project' => $project, 'feed' => $feed);
    }

    /**
     * @Route("/smells", name="code_project_smells")
     * @Template()
     */
    public function smellsAction($projectId)
    {
        $project = $this->getProject($projectId);
        $build = $this->getLatestBuild($project);

        $classes = $build->getClasses();

        return array('project' => $project, 'build' => $build, 'classes' => $classes);
    }

    /**
     * @Route("/classes", name="code_project_classes")
     * @Template()
     */
    public function classesAction($projectId)
    {
        $project = $this->getProject($projectId);
        $build = $this->getLatestBuild($project);

        $classes = $build->getClasses();

        return array('project' => $project, 'classes' => $classes);
    }
}
