<?php

namespace Classroom\ProjectBundle\Controller;

use Classroom\AnalyzerBundle\Loader\LoaderInterface;
use Classroom\PhpAnalyzerBundle\Node\PhpClassNode;
use Classroom\ProjectBundle\DataDirFactory;
use Classroom\ProjectBundle\Entity\Revision;
use Classroom\ProjectBundle\Entity\Project;
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
    /**
     * @param string $projectKey
     * @return Project
     */
    private function getProject($projectKey)
    {
        $entityManager = $this->get('doctrine.orm.entity_manager');
        /* @var $entityManager EntityManager */

        $repository = $entityManager->getRepository('Classroom\ProjectBundle\Entity\Project');
        $project = $repository->findOneBy(array('key' => $projectKey));

        return $project;
    }

    /**
     * @param Project $project
     * @return Revision
     */
    private function getLatestRevisionForProject(Project $project)
    {
        $entityManager = $this->get('doctrine.orm.entity_manager');
        /* @var $entityManager EntityManager */

        $repository = $entityManager->getRepository('Classroom\ProjectBundle\Entity\Revision');
        $revision = $repository->findOneBy(
            array(
                'project' => $project,
                'revision' => $project->getLatestBuildVersion()
            )
        );

        return $revision;
    }

    /**
     * @param Revision $revision
     * @return Result
     */
    private function getResultForRevision(Revision $revision)
    {
        $loader = $this->get('classroom.analyzer.loader');
        /* @var $loader LoaderInterface */

        $dataDirFactory = $this->get('classroom.project.data_dir_factory');
        /* @var $dataDirFactory DataDirFactory */

        $dataDir = $dataDirFactory->factory($revision->getProject());
        $result = $loader->load($dataDir->getBuildFile($revision->getResultFilename()));

        return $result;
    }

    /**
     * @Route("", name="classroom_project")
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
     * @Route("/overview", name="classroom_project_overview")
     * @Template()
     */
    public function overviewAction($projectKey)
    {
        $project = $this->getProject($projectKey);
        $revision = $this->getLatestRevisionForProject($project);

        return array('project' => $project, 'revision' => $revision);
    }

    /**
     * @Route("/feed", name="classroom_project_feed")
     * @Template()
     */
    public function feedAction($projectKey)
    {
        $project = $this->getProject($projectKey);
        $revision = $this->getLatestRevisionForProject($project);

        return array('project' => $project, 'changes' => array(), 'revision' => $revision);
    }

    /**
     * @Route("/smells", name="classroom_project_smells")
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
     * @Route("/nodes", name="classroom_project_nodes")
     * @Template()
     */
    public function nodesAction(Request $request, $projectKey)
    {
        $project = $this->getProject($projectKey);
        $revision = $this->getLatestRevisionForProject($project);
        $result = $this->getResultForRevision($revision);

        $grade = null;
        if ($request->query->has('grade')) {
            $grade = $request->query->get('grade');
        }

        $nodes = array();
        foreach ($result->getNodes() as $node) {
            if (!$node instanceof PhpClassNode) {
                continue;
            }
            if ($grade && $node->getGrade() !== $grade) {
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
