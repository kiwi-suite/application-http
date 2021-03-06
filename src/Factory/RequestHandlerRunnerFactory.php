<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\ApplicationHttp\Factory;

use Ixocreate\Application\ApplicationConfig;
use Ixocreate\ApplicationHttp\Middleware\MiddlewareSubManager;
use Ixocreate\ApplicationHttp\Middleware\SegmentMiddlewarePipe;
use Ixocreate\ApplicationHttp\Pipe\PipeConfig;
use Ixocreate\ApplicationHttp\Pipe\PipeConfigurator;
use Ixocreate\Contract\ServiceManager\FactoryInterface;
use Ixocreate\Contract\ServiceManager\ServiceManagerInterface;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\ServerRequestFactory;
use Zend\Expressive\Middleware\ErrorResponseGenerator;
use Zend\HttpHandlerRunner\Emitter\EmitterStack;
use Zend\HttpHandlerRunner\Emitter\SapiEmitter;
use Zend\HttpHandlerRunner\RequestHandlerRunner;

final class RequestHandlerRunnerFactory implements FactoryInterface
{
    /**
     * @param ServiceManagerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Zend\HttpHandlerRunner\Emitter\InvalidArgumentException
     * @return RequestHandlerRunner
     */
    public function __invoke(ServiceManagerInterface $container, $requestedName, array $options = null)
    {
        $pipeConfig = ($options !== null && isset($options[PipeConfig::class]) && $options[PipeConfig::class] instanceof PipeConfig) ? $options[PipeConfig::class] : new PipeConfig(new PipeConfigurator());
        $isDevelopment = $container->get(ApplicationConfig::class)->isDevelopment();

        $emitter = new EmitterStack();
        $emitter->push(new SapiEmitter());

        return new RequestHandlerRunner(
            $container->get(MiddlewareSubManager::class)->build(SegmentMiddlewarePipe::class, [PipeConfig::class => $pipeConfig]),
            $emitter,
            function () {
                return ServerRequestFactory::fromGlobals();
            },
            function (\Throwable $e) use ($isDevelopment) : ResponseInterface {
                $generator = new ErrorResponseGenerator($isDevelopment);
                return $generator($e, new ServerRequest(), new Response());
            }
        );
    }
}
