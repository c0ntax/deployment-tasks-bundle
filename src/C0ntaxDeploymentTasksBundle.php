<?php
declare(strict_types = 1);

namespace C0ntax\DeploymentTasksBundle;

use C0ntax\DeploymentTasksBundle\DependencyInjection\CompilerPass\ServiceCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class C0ntaxDeploymentTasksBundle
 */
class C0ntaxDeploymentTasksBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new ServiceCompilerPass());
    }
}
