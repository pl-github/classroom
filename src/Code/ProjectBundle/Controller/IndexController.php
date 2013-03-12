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
        $rootDir = $this->container->getParameter('kernel.root_dir');
        $projectLoader = $this->container->get('code.project.loader');

        $projectFiles = glob($rootDir . '/data/*/project.serialized');
        $projects = array();
        foreach ($projectFiles as $projectFile) {
            preg_match('#/([^/]+)/project\.serialized$#', $projectFile, $match);
            $projectId = $match[1];
            $projects[] = $projectLoader->load($projectId);
        }

        return array('projects' => $projects);
    }
}
