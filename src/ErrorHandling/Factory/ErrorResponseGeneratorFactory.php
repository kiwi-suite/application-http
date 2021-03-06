<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\ApplicationHttp\ErrorHandling\Factory;

use Ixocreate\ApplicationHttp\ErrorHandling\Response\ErrorResponseGenerator;
use Ixocreate\Application\ApplicationConfig;
use Ixocreate\Config\Config;
use Ixocreate\Contract\ServiceManager\FactoryInterface;
use Ixocreate\Contract\ServiceManager\ServiceManagerInterface;
use Ixocreate\Template\Renderer;
use Zend\Expressive\Middleware\WhoopsErrorResponseGenerator;

final class ErrorResponseGeneratorFactory implements FactoryInterface
{
    /**
     * @param ServiceManagerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @return ErrorResponseGenerator|mixed|WhoopsErrorResponseGenerator
     */
    public function __invoke(ServiceManagerInterface $container, $requestedName, array $options = null)
    {
        $develop = $container->get(ApplicationConfig::class)->isDevelopment();

        $config = $container->get(Config::class)->get('error');

        $renderer = $container->has(Renderer::class)
            ? $container->get(Renderer::class)
            : null;

        if ($develop === true) {
            return new WhoopsErrorResponseGenerator((new WhoopsFactory())($container, $requestedName, $options));
        }
        $template = isset($config['template_error'])
            ? $config['template_error']
            : ErrorResponseGenerator::TEMPLATE_DEFAULT;

        return new ErrorResponseGenerator($renderer, $template);
    }
}
