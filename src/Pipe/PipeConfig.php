<?php
/**
 * kiwi-suite/application-http (https://github.com/kiwi-suite/application-http)
 *
 * @package kiwi-suite/application-http
 * @see https://github.com/kiwi-suite/application-http
 * @copyright Copyright (c) 2010 - 2018 kiwi suite GmbH
 * @license MIT License
 */

declare(strict_types=1);
namespace Ixocreate\ApplicationHttp\Pipe;

use Ixocreate\Contract\Application\SerializableServiceInterface;

final class PipeConfig implements SerializableServiceInterface
{
    /**
     * @var array
     */
    private $routes = [];

    /**
     * @var array
     */
    private $middlewarePipe = [];

    /**
     * @var string
     */
    private $router;

    /**
     * PipeConfig constructor.
     * @param PipeConfigurator $pipeConfigurator
     */
    public function __construct(PipeConfigurator $pipeConfigurator)
    {
        $this->routes = $pipeConfigurator->getRoutes();
        $this->middlewarePipe = $pipeConfigurator->getMiddlewarePipe();
        $this->router = $pipeConfigurator->getRouter();
    }

    /**
     * @return array
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

    /**
     * @return array
     */
    public function getMiddlewarePipe(): array
    {
        return $this->middlewarePipe;
    }

    /**
     * @return string
     */
    public function router(): string
    {
        return $this->router;
    }

    /**
     * @return string
     */
    public function serialize()
    {
        return \serialize([
            'routes' => $this->routes,
            'middlewarePipe' => $this->middlewarePipe,
            'router' => $this->router,
        ]);
    }

    /**
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        $this->routes = [];
        $this->middlewarePipe = [];

        $array = \unserialize($serialized);

        if (!\is_array($array)) {
            return;
        }

        if (!empty($array['routes']) && \is_array($array['routes'])) {
            $this->routes = $array['routes'];
        }

        if (!empty($array['middlewarePipe']) && \is_array($array['middlewarePipe'])) {
            $this->middlewarePipe = $array['middlewarePipe'];
        }

        if (!empty($array['router']) && \is_string($array['router'])) {
            $this->router = $array['router'];
        }
    }
}
