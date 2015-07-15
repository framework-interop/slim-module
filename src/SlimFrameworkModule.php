<?php

namespace Interop\Framework\Slim;

use Acclimate\Container\Adapter\PimpleContainerAdapter;
use Interop\Framework\Module;
use Interop\Container\ContainerInterface;
use Mouf\Interop\Silex\Application;
use Mouf\PrefixerContainer\DelegateLookupUnprefixerContainer;
use Mouf\PrefixerContainer\PrefixerContainer;
use Mouf\StackPhp\SilexMiddleware;
use Interop\Framework\HttpModuleInterface;
use Slim\App;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * This module provides a base Silex application.
 * Other modules can hook on that application.
 */
class SlimFrameworkModule implements HttpModuleInterface
{
    const SLIM_APP_ENTRY = "slimApp";

    private $rootContainer;
    private $slim;
    private $prefix;

    /**
     * @param string $prefix The prefix to use for all the container entries.
     */
    public function __construct($prefix = null) {
        $this->prefix = $prefix;
    }

    public function getName()
    {
        return 'slim';
    }

    public function getContainer(ContainerInterface $rootContainer)
    {
        $this->rootContainer = $rootContainer;

        if ($this->prefix) {
            $this->slim = new App(/*new DelegateLookupUnprefixerContainer($this->rootContainer, $this->prefix)*/);
        } else {
            $this->slim = new App(/*$this->rootContainer*/);
        }

        // Let's put the slim app in the container... that is itself the slim app :)
        $this->slim->getContainer()[self::SLIM_APP_ENTRY] = $this->slim;

        if ($this->prefix) {
            return new PrefixerContainer($this->slim->getContainer(), $this->prefix);
        } else {
            return $this->slim->getContainer();
        }
    }

    /* (non-PHPdoc)
     * @see \Interop\Framework\ModuleInterface::init()
     */
    public function init()
    {
    }

    /* (non-PHPdoc)
     * @see \Interop\Framework\HttpModuleInterface::getHttpMiddleware()
     */
    public function getHttpMiddleware()
    {
        return new SlimMiddleware($this->slim);
    }

    /**
     * @return App
     */
    public function getSlimApp() {
        return $this->slim;
    }
}
