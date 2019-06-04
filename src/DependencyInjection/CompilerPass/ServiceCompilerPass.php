<?php
declare(strict_types = 1);

namespace C0ntax\DeploymentTasksBundle\DependencyInjection\CompilerPass;

use C0ntax\DeploymentTasks\Service\TaskService;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\BadMethodCallException;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class ServiceCompilerPass
 */
class ServiceCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     *
     * @throws BadMethodCallException
     * @throws InvalidArgumentException
     */
    public function process(ContainerBuilder $container): void
    {
        $config = $container->getParameter('c0ntax_deployment_tasks');

        $taskFilesystemServiceId = $config['filesystems']['tasks'];
        $memoryFilesystemServiceId = $config['filesystems']['memory'];

        $definition = new Definition(TaskService::class, [new Reference($taskFilesystemServiceId), new Reference($memoryFilesystemServiceId)]);
        $container->setDefinition('c0ntax_deployment_tasks.service.task', $definition);
    }
}
