<?php

namespace Code\ProjectBundle\Controller;

use Code\AnalyzerBundle\Loader\LoaderInterface;
use Code\PhpAnalyzerBundle\Node\PhpClassNode;
use Code\ProjectBundle\Entity\Revision;
use Code\ProjectBundle\Entity\Project;
use Code\ProjectBundle\Loader\LoaderInterface as ProjectLoaderInterface;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/project/{projectKey}")
 */
class ProjectController extends Controller
{
    private function getProject($projectKey)
    {
        $entityManager = $this->get('doctrine.orm.entity_manager');
        /* @var $entityManager EntityManager */

        $repository = $entityManager->getRepository('Code\ProjectBundle\Entity\Project');
        $project = $repository->findOneBy(array('key' => $projectKey));

        return $project;
    }

    private function getLatestRevisionForProject(Project $project)
    {
        $entityManager = $this->get('doctrine.orm.entity_manager');
        /* @var $entityManager EntityManager */

        $repository = $entityManager->getRepository('Code\ProjectBundle\Entity\Revision');
        $revision = $repository->findOneBy(array('project' => $project));

        return $revision;
    }

    private function getResultForRevision(Revision $revision)
    {
        $loader = $this->get('code.analyzer.loader');
        /* @var $loader LoaderInterface */

        $result = $loader->load($revision->getResultFilename());

        return $result;
    }

    /**
     * @Route("", name="code_project")
     * @Template()
     */
    public function indexAction(Request $request, $projectKey)
    {
        $card = $request->query->get('card');

        $project = $this->getProject($projectKey);
        $revision = $this->getLatestRevisionForProject($project);

        return array('card' => $card, 'project' => $project, 'revision' => $revision);
    }

    /**
     * @Route("/feed", name="code_project_feed")
     * @Template()
     */
    public function feedAction($projectKey)
    {
        $project = $this->getProject($projectKey);
        $revision = $this->getLatestRevisionForProject($project);

        return array('project' => $project, 'changes' => array(), 'revision' => $revision);
    }

    /**
     * @Route("/smells", name="code_project_smells")
     * @Template()
     */
    public function smellsAction($projectKey)
    {
        $project = $this->getProject($projectKey);
        $revision = $this->getLatestRevisionForProject($project);
        $result = $this->getResultForRevision($revision);

        $smells = array();
        foreach ($result->getSmells() as $smell) {
            $classNode = $result->getNode($result->getReference('smell', 'smellToNode', $smell));
            $smells[] = array(
                'origin' => $smell->getOrigin(),
                'rule' => $smell->getRule(),
                'nodeName' => $classNode->getName(),
            );
        }

        return array('project' => $project, 'revision' => $revision, 'smells' => $smells);
    }

    /**
     * @Route("/classes", name="code_project_classes")
     * @Template()
     */
    public function nodesAction($projectKey)
    {
        $project = $this->getProject($projectKey);
        $revision = $this->getLatestRevisionForProject($project);
        $result = $this->getResultForRevision($revision);

        $nodes = array();
        foreach ($result->getNodes() as $node) {
            if (!$node instanceof PhpClassNode) {
                continue;
            }
            $nodes[] = array(
                'nodeName' => $node->getName(),
                'grade' => $node->getGrade(),
                'numSmells' => $result->hasReference('smell', 'nodeToSmells', $node) ? count($result->getReference('smell', 'nodeToSmells', $node)) : 0,
                'metrics' => $node->getMetrics(),
            );
        }

        return array('project' => $project, 'revision' => $revision, 'nodes' => $nodes);
    }
}
