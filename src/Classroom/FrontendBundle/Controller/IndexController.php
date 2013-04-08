<?php

namespace Classroom\FrontendBundle\Controller;

use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class IndexController extends Controller
{
    /**
     * @Route("/", name="classroom_index")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $entityManager = $this->get('doctrine.orm.entity_manager');
        /* @var $entityManager EntityManager */

        $repository = $entityManager->getRepository('Classroom\ProjectBundle\Entity\Project');

        $projects = $repository->findAll();

        return array('projects' => $projects);
    }
}
