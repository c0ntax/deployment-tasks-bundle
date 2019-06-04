<?php
declare(strict_types = 1);

namespace C0ntax\DeploymentTasksBundle\Command;

use C0ntax\DeploymentTasks\Entity\Task;
use C0ntax\DeploymentTasks\Service\RunnerService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Exception\InvalidArgumentException;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Exception\RuntimeException;

/**
 * Class DeploymentTasksRunCommand
 */
class DeploymentTasksRunCommand extends Command
{
    /** @var RunnerService */
    private $runnerService;

    /** @var SymfonyStyle */
    private $io;

    /**
     * DeploymentTasksRunCommand constructor.
     *
     * @param RunnerService $runnerService
     *
     * @throws LogicException
     */
    public function __construct(RunnerService $runnerService)
    {
        parent::__construct();
        $this->setRunnerService($runnerService);
    }

    /**
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     */
    protected function configure(): void
    {
        $this
            ->setName('deployment:tasks:run')
            ->setDescription('Run all tasks that have not yet been run')
            ->addArgument('task-type', InputArgument::REQUIRED, 'Either Pre or Post');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|void|null
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     * @throws InvalidArgumentException
     * @throws \Symfony\Component\Process\Exception\LogicException
     * @throws ProcessFailedException
     * @throws RuntimeException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->setIo(new SymfonyStyle($input, $output));
        $taskType = $input->getArgument('task-type');

        $this->getIo()->title('Run Deployment Tasks');
        $this->getIo()->comment(sprintf('Task Type set to <info>%s</info>', $taskType));

        $tasks = $this->getRunnerService()->run($taskType);

        $this->getIo()->success('Completed');

        $this->getIo()->table(['id', 'run', 'success'], $this->createTableOfTasks($tasks));
    }

    /**
     * @param Task[] $tasks
     *
     * @return array
     */
    private function createTableOfTasks(array $tasks): array
    {
        $out = [];
        foreach ($tasks as $task) {
            $out[] = [$task->getId(), $this->createStringFromBool($task->isRun()), $this->createStringFromBool($task->isSuccess())];
        }

        return $out;
    }

    /**
     * @param bool $bool
     *
     * @return string
     */
    private function createStringFromBool(bool $bool): string
    {
        return $bool ? "\u2713" : "\u274C";
    }

    /**
     * @return RunnerService
     */
    private function getRunnerService(): RunnerService
    {
        return $this->runnerService;
    }

    /**
     * @param RunnerService $runnerService
     *
     * @return DeploymentTasksRunCommand
     */
    private function setRunnerService(RunnerService $runnerService): DeploymentTasksRunCommand
    {
        $this->runnerService = $runnerService;

        return $this;
    }

    /**
     * @return SymfonyStyle
     */
    private function getIo(): SymfonyStyle
    {
        return $this->io;
    }

    /**
     * @param SymfonyStyle $io
     *
     * @return DeploymentTasksRunCommand
     */
    private function setIo(SymfonyStyle $io): DeploymentTasksRunCommand
    {
        $this->io = $io;

        return $this;
    }
}
