<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\ApplicationHttp\Middleware;

use Ixocreate\Contract\Application\ConfiguratorInterface;
use Ixocreate\Contract\Application\ServiceRegistryInterface;
use Ixocreate\ServiceManager\Factory\AutowireFactory;
use Ixocreate\ServiceManager\SubManager\SubManagerConfigurator;
use Psr\Http\Server\MiddlewareInterface;

final class MiddlewareConfigurator implements ConfiguratorInterface
{
    /**
     * @var SubManagerConfigurator
     */
    private $subManagerConfigurator;

    /**
     * MiddlewareConfigurator constructor.
     */
    public function __construct()
    {
        $this->subManagerConfigurator = new SubManagerConfigurator(MiddlewareSubManager::class, MiddlewareInterface::class);
    }

    /**
     * @param string $directory
     * @param bool $recursive
     */
    public function addDirectory(string $directory, bool $recursive = true): void
    {
        $this->subManagerConfigurator->addDirectory($directory, $recursive);
    }

    /**
     * @param string $action
     * @param string $factory
     */
    public function addAction(string $action, string $factory = AutowireFactory::class): void
    {
        $this->subManagerConfigurator->addFactory($action, $factory);
    }

    /**
     * @param string $action
     * @param string $factory
     */
    public function addMiddleware(string $action, string $factory = AutowireFactory::class): void
    {
        $this->subManagerConfigurator->addFactory($action, $factory);
    }

    /**
     * @return SubManagerConfigurator
     */
    public function getManagerConfigurator(): SubManagerConfigurator
    {
        return $this->subManagerConfigurator;
    }

    /**
     * @param ServiceRegistryInterface $serviceRegistry
     * @return void
     */
    public function registerService(ServiceRegistryInterface $serviceRegistry): void
    {
        $this->subManagerConfigurator->registerService($serviceRegistry);
    }
}
