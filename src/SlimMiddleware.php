<?php

namespace Interop\Framework\Slim;

use Acclimate\Container\Adapter\PimpleContainerAdapter;
use Interop\Framework\Module;
use Interop\Container\ContainerInterface;
use Mouf\PrefixerContainer\DelegateLookupUnprefixerContainer;
use Mouf\PrefixerContainer\PrefixerContainer;
use Interop\Framework\HttpModuleInterface;
use Slim\App;
use Zend\Stratigility\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;


/**
 * This classe changes a Slim application into a PSR-7 middleware.
 */
class SlimMiddleware implements MiddlewareInterface
{
    private $request;
    private $type;
    private $catch;
    private $slim;

    /**
     * @param App $slim The Slim application that will try catching requests
     */
    public function __construct(App $slim) {
        $this->slim = $slim;
    }

    /**
     * Process an incoming request and/or response.
     *
     * Accepts a server-side request and a response instance, and does
     * something with them.
     *
     * If the response is not complete and/or further processing would not
     * interfere with the work done in the middleware, or if the middleware
     * wants to delegate to another process, it can use the `$out` callable
     * if present.
     *
     * If the middleware does not return a value, execution of the current
     * request is considered complete, and the response instance provided will
     * be considered the response to return.
     *
     * Alternately, the middleware may return a response instance.
     *
     * Often, middleware will `return $out();`, with the assumption that a
     * later middleware will return a response.
     *
     * @param Request $request
     * @param Response $response
     * @param null|callable $out
     * @return null|Response
     */
    public function __invoke(Request $request, Response $response, callable $out = null) {
        $container = $this->slim->getContainer();

        // Let's init the request and response
        $container['request'] = $request;
        $container['response'] = $response;

        $container['notFoundHandler'] = function ($c) use ($out) {
            return $out;
        };

        return $this->slim->run();
    }
}
