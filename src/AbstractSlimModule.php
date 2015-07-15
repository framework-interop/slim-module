<?php

namespace Interop\Framework\Slim;

use Interop\Framework\Module;
use Interop\Container\ContainerInterface;
use Interop\Framework\ModuleInterface;
use Interop\Framework\Silex\SlimFrameworkModule;
use Interop\Container\Exception\NotFoundException;

/**
 * If you need to write a module that relies on Slim, your module can extend this class.
 */
abstract class AbstractSlimModule implements ModuleInterface
{
    /**
     * @var ContainerInterface
     */
    private $rootContainer;

    private $slimFrameworkModule;

    /**
     * @param string $prefix The prefix to use for all the container entries.
     */
    public function __construct(SlimFrameworkModule $slimFrameworkModule) {
        $this->slimFrameworkModule = $slimFrameworkModule;
    }

    abstract public function getName();

    public function getContainer(ContainerInterface $rootContainer)
    {
        $this->rootContainer = $rootContainer;
        return;
    }

    abstract public function init();

    /**
     * @return Application
     */
    protected function getSlimApp()
    {
        return $this->slimFrameworkModule->getSlimApp();
    }

    /**
     * @return ContainerInterface
     */
    protected function getRootContainer()
    {
        return $this->rootContainer;
    }
}
