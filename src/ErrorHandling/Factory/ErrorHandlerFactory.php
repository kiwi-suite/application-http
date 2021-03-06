<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\ApplicationHttp\ErrorHandling\Factory;

use Ixocreate\ApplicationHttp\ErrorHandling\Response\ErrorResponseGenerator;
use Ixocreate\Contract\ServiceManager\ServiceManagerInterface;
use Ixocreate\Contract\ServiceManager\FactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response;
use Zend\Stratigility\Middleware\ErrorHandler;

final class ErrorHandlerFactory implements FactoryInterface
{
    /**
     * @param ServiceManagerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return mixed|ErrorHandler
     */
    public function __invoke(ServiceManagerInterface $container, $requestedName, array $options = null)
    {
        $generator = $container->has(ErrorResponseGenerator::class)
            ? function (
                \Throwable $e,
                ServerRequestInterface $request,
                ResponseInterface $response
            ) use ($container) {
                $generator = $container->get(ErrorResponseGenerator::class);
                return $generator($e, $request, $response);
            }
        : null;

        return new ErrorHandler(
            function () {
                return new Response();
            },
            $generator
        );
    }
}
