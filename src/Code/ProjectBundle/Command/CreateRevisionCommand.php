<?php

namespace Code\ProjectBundle\Command;

use Code\AnalyzerBundle\Log\Log;
use Code\ProjectBundle\Builder;
use Code\ProjectBundle\Entity\Project;
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
        /* @var $project Project */

        if (!$project) {
            $output->writeln('<error>Project ' . $projectKey . ' not found.</error>');
            return 1;
        }

        $revision = new Revision();
        $revision
            ->setProject($project)
            ->setStatus(Revision::STATUS_NEW)
            ->setCreateAt(new \DateTime());
        $entityManager->persist($revision);
        $entityManager->flush($revision);

        $output->writeln('<info>New revision for ' . $projectKey . ' created.</info>');

        if (!$now) {
            $job = new Job('code:project:build', array($revision->getId()));
            $entityManager->persist($job);
            $entityManager->flush($job);

            $output->writeln('<info>Build queued.</info>');
        } else {
            $builder = $this->getContainer()->get('code.project.builder');
            /* @var $builder Builder */

            $resultFilename = $builder->build($revision, function($type, $line) use ($output) {
                switch ($type) {
                    case Log::TYPE_COMMAND:
                        $output->writeln('<comment>$ ' . $line . '</comment>');
                        break;
                    case Log::TYPE_ERROR:
                        $output->writeln('<error>' . $line . '</error>');
                        break;
                    case Log::TYPE_PROCESS:
                        $output->writeln('<comment>' . $line . '</comment>');
                        break;
                    case Log::TYPE_OUTPUT:
                    default:
                        $output->writeln($line);
                        break;
                }
            });

            $entityManager->persist($revision);
            $entityManager->flush($revision);

            $project->setLatestBuildVersion($revision->getRevision());

            $entityManager->persist($project);
            $entityManager->flush($project);

            $output->writeln('<info>Build finished.</info>');
            $output->writeln('<info>Result written to ' . $resultFilename . '</info>');
        }
    }
}
