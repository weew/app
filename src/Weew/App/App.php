<?php

namespace Weew\App;

use Weew\Container\Container;
use Weew\Container\IContainer;
use Weew\Eventer\ContainerAware\Eventer as ContainerAwareEventer;
use Weew\Eventer\Eventer;
use Weew\Eventer\IEventer;
use Weew\App\Events\App\AppShutdownEvent;
use Weew\App\Events\App\AppStartedEvent;
use Weew\App\Events\Kernel\KernelBootedEvent;
use Weew\App\Events\Kernel\KernelInitializedEvent;
use Weew\App\Events\Kernel\KernelShutdownEvent;
use Weew\Kernel\ContainerAware\Kernel as ContainerAwareKernel;
use Weew\Kernel\IKernel;
use Weew\Kernel\Kernel;

class App implements IApp {
    /**
     * @var IContainer
     */
    protected $container;

    /**
     * @var IKernel
     */
    protected $kernel;

    /**
     * @var IEventer
     */
    protected $eventer;

    /**
     * App constructor.
     */
    public function __construct() {
        $this->createEssentialComponents();
    }

    /**
     * Get dependency injection container instance.
     *
     * @return IContainer
     */
    public function getContainer() {
        return $this->container;
    }

    /**
     * Get app kernel instance.
     *
     * @return IKernel
     */
    public function getKernel() {
        return $this->kernel;
    }

    /**
     * Get event bus instance.
     *
     * @return IEventer
     */
    public function getEventer() {
        return $this->eventer;
    }

    /**
     * Dry run - start and shutdown app.
     * This method is not meant to be used as
     * the main entry point in to the App.
     */
    public function run() {
        $this->start();
        $this->shutdown();
    }

    /**
     * Start App.
     */
    protected function start() {
        $this->kernel->initialize();
        $this->eventer->dispatch(new KernelInitializedEvent($this->kernel));

        $this->kernel->boot();
        $this->eventer->dispatch(new KernelBootedEvent($this->kernel));

        $this->eventer->dispatch(new AppStartedEvent($this));
    }

    /**
     * Shutdown App.
     */
    protected function shutdown() {
        $this->kernel->shutdown();
        $this->eventer->dispatch(new KernelShutdownEvent($this->kernel));
        $this->eventer->dispatch(new AppShutdownEvent($this));
    }

    /**
     * Create essential components.
     */
    protected function createEssentialComponents() {
        $this->container = $this->createContainer();
        $this->kernel = $this->createKernel();
        $this->eventer = $this->createEventer();

        $this->container->set([App::class, IApp::class], $this);
    }

    /**
     * @return IContainer
     */
    protected function createContainer() {
        return new Container();
    }

    /**
     * @return IKernel
     */
    protected function createKernel() {
        $kernel = $this->container->get(ContainerAwareKernel::class);
        $this->container->set([Kernel::class, IKernel::class], $kernel);

        return $kernel;
    }

    /**
     * @return IEventer
     */
    protected function createEventer() {
        $eventer = $this->container->get(ContainerAwareEventer::class);
        $this->container->set([Eventer::class, IEventer::class], $eventer);

        return $eventer;
    }
}
