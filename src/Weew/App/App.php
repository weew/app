<?php

namespace Weew\App;

use Weew\App\Events\ConfigLoadedEvent;
use Weew\App\Events\KernelBootedEvent;
use Weew\App\Events\KernelInitializedEvent;
use Weew\App\Events\KernelShutdownEvent;
use Weew\App\Exceptions\ConfigNotLoadedException;
use Weew\Config\Config;
use Weew\Config\ConfigLoader;
use Weew\Config\IConfig;
use Weew\Config\IConfigLoader;
use Weew\Container\Container;
use Weew\Container\IContainer;
use Weew\Eventer\ContainerAware\Eventer as ContainerAwareEventer;
use Weew\Eventer\Eventer;
use Weew\Eventer\IEventer;
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
     * @var IConfigLoader
     */
    protected $configLoader;

    /**
     * @var IConfig
     */
    protected $config;

    /**
     * App constructor.
     */
    public function __construct() {
        $this->container = $this->createContainer();
        $this->kernel = $this->createKernel();
        $this->eventer = $this->createEventer();
        $this->configLoader = $this->createConfigLoader();

        $this->container->set([App::class, IApp::class], $this);
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
     * @return IConfigLoader
     */
    public function getConfigLoader() {
        return $this->configLoader;
    }

    /**
     * @return IConfig
     * @throws ConfigNotLoadedException
     */
    public function getConfig() {
        if ($this->config === null) {
            throw new ConfigNotLoadedException(
                'Config has not been loaded yet. ' .
                'Config is loaded after the application startup.'
            );
        }

        return $this->config;
    }

    /**
     * Load configuration.
     */
    public function loadConfig() {
        if ( ! $this->config instanceof IConfig) {
            $this->config = $this->configLoader->load();
            $this->container->set([Config::class, IConfig::class], $this->config);
        }

        $this->eventer->dispatch(new ConfigLoadedEvent($this->config));
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
        $this->loadConfig();
        $this->startKernel();
        $this->eventer->dispatch(new Events\AppStartedEvent($this));
    }

    /**
     * Shutdown App.
     */
    protected function shutdown() {
        $this->kernel->shutdown();
        $this->eventer->dispatch(new KernelShutdownEvent($this->kernel));
        $this->eventer->dispatch(new Events\AppShutdownEvent($this));
    }

    /**
     * Initialize and boot kernel.
     */
    protected function startKernel() {
        $this->kernel->initialize();
        $this->eventer->dispatch(new KernelInitializedEvent($this->kernel));

        $this->kernel->boot();
        $this->eventer->dispatch(new KernelBootedEvent($this->kernel));
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

    /**
     * @return IConfigLoader
     */
    protected function createConfigLoader() {
        $configLoader = $this->container->get(ConfigLoader::class);
        $this->container->set([ConfigLoader::class, IConfigLoader::class], $configLoader);

        return $configLoader;
    }
}
