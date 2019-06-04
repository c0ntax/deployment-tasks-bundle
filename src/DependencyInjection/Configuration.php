<?php
declare(strict_types = 1);

namespace C0ntax\DeploymentTasksBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Class Configuration
 */
class Configuration implements ConfigurationInterface
{
    /**
     * @return TreeBuilder|void
     * @throws \RuntimeException
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = null;
        $rootNode = null;
        if (Kernel::MAJOR_VERSION < 4) {
            $treeBuilder = new TreeBuilder();
            $rootNode = $treeBuilder->root('c0ntax_deployment_tasks');
        } else {
            $treeBuilder = new TreeBuilder('c0ntax_deployment_tasks');
            $rootNode = $treeBuilder->getRootNode();
        }

        // @formatter:off
        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('filesystems')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('tasks')->isRequired()->defaultValue('tasks_filesystem')->info('A Gaufrette service id for the location of all the tasks to run')->end()
                        ->scalarNode('memory')->isRequired()->defaultValue('memory_filesystem')->info('A Gaufrette service id for the location of all the tasks that have been run')->end()
                    ->end()
                ->end()
            ->end();
        // @formatter:on

        return $treeBuilder;
    }
}
