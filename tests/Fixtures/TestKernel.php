<?php
declare(strict_types = 1);

namespace C0ntax\DeploymentTasksBundle\Tests\Fixtures;

use C0ntax\DeploymentTasksBundle\C0ntaxDeploymentTasksBundle;
use Exception;
use Knp\Bundle\GaufretteBundle\KnpGaufretteBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Class TestKernel
 */
class TestKernel extends Kernel
{
    /**
     * @return array|iterable|BundleInterface[]
     */
    public function registerBundles(): array
    {
        return [
            new FrameworkBundle(),
            new KnpGaufretteBundle(),
            new C0ntaxDeploymentTasksBundle(),
        ];
    }

    /**
     * @param LoaderInterface $loader
     *
     * @throws Exception
     */
    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__.'/config/config.yaml');
        $loader->load(__DIR__.'/config/services.yaml');
        $loader->load(__DIR__.'/config/parameters.yaml');
    }

    public function getCacheDir()
    {
        return $this->getProjectDir().'/tests/Fixtures/cache/'.$this->environment;
    }
}
