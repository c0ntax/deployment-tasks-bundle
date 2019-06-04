<?php

namespace C0ntax\DeploymentTasksBundle\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class DeploymentTasksRunCommandTest extends KernelTestCase
{
    public function testExecute()
    {
        $this->createTestData();

        $kernel = static::createKernel();
        $application = new Application($kernel);
        $command = $application->find('deployment:tasks:run');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['task-type' => 'Pre']);
        $output = $commandTester->getDisplay();

        self::assertStringContainsString('Pre/task20190604900000.sh', $output);
        self::assertStringContainsString('Pre/task20190604900001.sh', $output);

        // Run it again, and nothing should happen
        $commandTester = new CommandTester($command);
        $commandTester->execute(['task-type' => 'Pre']);
        $output = $commandTester->getDisplay();

        self::assertStringNotContainsString('Pre/task20190604900000.sh', $output);
        self::assertStringNotContainsString('Pre/task20190604900001.sh', $output);

        // Now for the Post commands
        $commandTester = new CommandTester($command);
        $commandTester->execute(['task-type' => 'Post']);
        $output = $commandTester->getDisplay();

        self::assertStringContainsString('Post/task20190604900001.sh', $output);
        self::assertStringContainsString('Post/task20190604900002.sh', $output);

        // Now for the Post commands, and again with nothing to do
        $commandTester = new CommandTester($command);
        $commandTester->execute(['task-type' => 'Post']);
        $output = $commandTester->getDisplay();

        self::assertStringNotContainsString('Post/task20190604900001.sh', $output);
        self::assertStringNotContainsString('Post/task20190604900002.sh', $output);
    }

    private function createTestData(): void
    {
        $tasks = __DIR__.'/../Fixtures/DeploymentTasks';
        $memory = __DIR__.'/../Fixtures/Memory';

        $map = [
            'tasks' => [
                'Pre' => [
                    'task20190604900000.sh' => 'echo "pre-1"',
                    'task20190604900001.sh' => 'echo "pre-2"',
                ],
                'Post' => [
                    'task20190604900000.sh' => 'echo "post-1"',
                    'task20190604900001.sh' => 'echo "post-2"',
                    'task20190604900002.sh' => 'echo "post-3"',
                ],
            ],
            'memory' => [
                'Pre' => [

                ],
                'Post' => [
                    'task20190604900000.sh' => (string) time(),
                ]
            ]
        ];

        foreach ($map['tasks'] as $taskType => $taskArray) {
            $dir = sprintf('%s/%s', $tasks, $taskType);
            $this->wipeDir($dir);
            foreach ($taskArray as $id => $command) {
                $filename = sprintf('%s/%s', $dir, $id);
                $commandData = <<<EOF
#!/usr/bin/env bash

${command}
EOF;

                file_put_contents($filename, $commandData);
                chmod($filename, 0775);
            }
        }

        foreach ($map['memory'] as $taskType => $taskArray) {
            $dir = sprintf('%s/%s', $memory, $taskType);
            $this->wipeDir($dir);
            foreach ($taskArray as $id => $data) {
                $filename = sprintf('%s/%s', $dir, $id);
                file_put_contents($filename, $data);
            }
        }
    }

    private function wipeDir(string $dir): void
    {
        $files = glob(sprintf('%s/*.sh', $dir));
        foreach ($files as $file) { // iterate files
            if (is_file($file)) {
                unlink($file);
            } // delete file
        }
    }
}
