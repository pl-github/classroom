<?php

namespace Code\ProjectBundle\Controller;

use Code\AnalyzerBundle\Result\Result;
use Code\ProjectBundle\DataDirFactory;
use Code\ProjectBundle\Entity\Project;
use Code\ProjectBundle\Entity\Revision;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/project/{projectKey}/node/{nodeName}")
 */
class NodeController extends Controller
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
        $loader = $this->get('code.analyzer.loader');
        /* @var $loader LoaderInterface */

        $dataDirFactory = $this->get('code.project.data_dir_factory');
        /* @var $dataDirFactory DataDirFactory */

        $dataDir = $dataDirFactory->factory($revision->getProject());
        $result = $loader->load($dataDir->getBuildFile($revision->getResultFilename()));

        return $result;
    }

    /**
     * @Route("", name="code_node")
     * @Template()
     */
    public function indexAction($projectKey, $nodeName)
    {
        $project = $this->getProject($projectKey);
        $revision = $this->getLatestRevisionForProject($project);
        $result = $this->getResultForRevision($revision);

        $node = $result->getNode($nodeName);

        $hasSmells = false;
        if ($result->hasReference('smell', 'nodeToSmells', $node)) {
            $hasSmells = true;
        }

        return array('project' => $project, 'node' => $node, 'hasSmells' => $hasSmells);
    }

    /**
     * @Route("/metrics", name="code_node_metrics")
     * @Template()
     */
    public function metricsAction($projectKey, $nodeName)
    {
        $project = $this->getProject($projectKey);
        $revision = $this->getLatestRevisionForProject($project);
        $result = $this->getResultForRevision($revision);

        $node = $result->getNode($nodeName);
        $metrics = $node->getMetrics();

        return array('metrics' => $metrics);
    }

    /**
     * @Route("/smells", name="code_node_smells")
     * @Template()
     */
    public function smellsAction($projectKey, $nodeName)
    {
        $project = $this->getProject($projectKey);
        $revision = $this->getLatestRevisionForProject($project);
        $result = $this->getResultForRevision($revision);

        $node = $result->getNode($nodeName);
        $fileNode = $result->getReference('node', 'parent', $node);
        if ($result->hasReference('source', 'nodeToSource', $fileNode)) {
            $source = $result->getSource($result->getReference('source', 'nodeToSource', $fileNode));
        }

        $smells = array();
        foreach ($result->getReference('smell', 'nodeToSmells', $node) as $id => $smellReference) {
            $smell = $result->getSmell($smellReference);

            $sourceData = null;
            if ($source) {
                $sourceData = array(
                    'files' => null,
                    'content' => $source->getRange($smell->getSourceRange())
                );
            }

            $smells[] = array(
                'id' => $id,
                'origin' => $smell->getOrigin(),
                'rule' => $smell->getRule(),
                'text' => $smell->getText(),
                'score' => $smell->getScore(),
                'source' => $sourceData
            );
        }

        return array('smells' => $smells);
    }

    /**
     * @Route("/annotations", name="code_node_annotations")
     * @Template()
     */
    public function annotationsAction($projectKey, $nodeName)
    {
        $project = $this->getProject($projectKey);
        $revision = $this->getLatestRevisionForProject($project);
        $result = $this->getResultForRevision($revision);

        $node = $result->getNode($nodeName);
        if (!$result->hasReference('node', 'parent', $node)) {
            return array('content' => 'File not found');
        }
        $fileNode = $result->getReference('node', 'parent', $node);
        if (!$result->hasReference('source', 'nodeToSource', $fileNode)) {
            return array('content' => 'Source not found');
        }

        $source = $result->getSource($result->getReference('source', 'nodeToSource', $fileNode));
        $contentData = $source->getContentAsArray();

        $lines = array();
        foreach ($contentData as $lineNo => $line) {
            $lines[$lineNo + 1] = array(
                'no' => $lineNo + 1,
                'line' => $line,
                'annotations' => array(),
            );
        }

        foreach ($result->getReference('smell', 'nodeToSmells', $node) as $id => $smellReference) {
            $smell = $result->getSmell($smellReference);
            $sourceRange = $smell->getSourceRange();
            $beginLine = $sourceRange->getBeginLine();
            $endLine = $sourceRange->getEndLine();

            for ($i = $beginLine; $i <= $endLine; $i++) {
                $lines[$i]['annotations'][] = $smell->getRule();
                $lines[$i]['bla'] = $i;
            }
        }

        $max = 0;
        foreach ($lines as $lineNo => $line) {
            $lines[$lineNo]['annotations'] = implode(',', $line['annotations']);
            $max = max($max, strlen($lines[$lineNo]['annotations']));
        }

        $content = '';
        foreach ($lines as $lineNo => $line) {
            $content .= str_pad($lineNo, 6, ' ', STR_PAD_LEFT) . ' | ';
            if ($line['annotations']) {
                $content .= '<span style="color: red">';
            }
            $content .= str_pad($line['annotations'], $max, ' ') . ' | ';
            if ($line['annotations']) {
                $content .= '</span>';
                $content .= '<span style="color: red">';
            }
            $content .= htmlentities($line['line']) . PHP_EOL;
            if ($line['annotations']) {
                $content .= '</span>';
            }
        }

        return array('content' => $content);

    }
}
