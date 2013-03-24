<?php

namespace Code\ProjectBundle\Command;

use Code\ProjectBundle\Builder;
use Code\ProjectBundle\Entity\Revision;
use Doctrine\ORM\EntityManager;
use JMS\JobQueueBundle\Entity\Job;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateRevisionCommand extends ContainerAwareCommand
{
    protected $name;

    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('code:project:create-revision')
            ->setDescription('Create revision')
            ->addArgument('projectKey', InputArgument::REQUIRED)
            ->addOption('now', '', InputOption::VALUE_NONE, 'Build now');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $projectKey = $input->getArgument('projectKey');
        $now = $input->getOption('now');

        $entityManager = $this->getContainer()->get('doctrine.orm.entity_manager');
        /* @var $projectLoader EntityManager */
        $projectRepository = $entityManager->getRepository('Code\ProjectBundle\Entity\Project');

        $project = $projectRepository->findOneBy(array('key' => $projectKey));

        $revision = new Revision();
        $revision
            ->setProject($project)
            ->setStatus(Revision::STATUS_NEW)
            ->setCreateAt(new \DateTime());
        $entityManager->persist($revision);
        $entityManager->flush($revision);

        $output->writeln('New revision for ' . $projectKey . ' created.');

        if (!$now) {
            $job = new Job('code:project:build', array($revision->getId()));
            $entityManager->persist($job);
            $entityManager->flush($job);

            $output->writeln('Build queued.');

        } else {
            $builder = $this->getContainer()->get('code.project.builder');
            /* @var $builder Builder */

            $builder->build($revision);

            $entityManager->persist($revision);
            $entityManager->flush($revision);

            $output->writeln('Build finished.');
        }

    }
}
