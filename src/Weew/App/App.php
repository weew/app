<?php

namespace Weew\App;

use Weew\App\Events\AppShutdownEvent;
use Weew\App\Events\AppStartedEvent;
use Weew\App\Events\ConfigLoadedEvent;
use Weew\App\Events\KernelBootedEvent;
use Weew\App\Events\KernelInitializedEvent;
use Weew\App\Events\KernelShutdownEvent;
use Weew\Commander\Commander;
use Weew\Commander\ContainerAware\Commander as ContainerAwareCommander;
use Weew\Commander\ICommander;
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
     * @var bool
     */
    protected $started = false;

    /**
     * @var string
     */
    protected $environment;

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
     * @var ICommander
     */
    protected $commander;

    /**
     * @var IConfig
     */
    protected $config;

    /**
     * App constructor.
     *
     * @param string $environment
     */
    public function __construct($environment = 'prod') {
        $this->setEnvironment($environment);

        $this->container = $this->createContainer();
        $this->kernel = $this->createKernel();
        $this->eventer = $this->createEventer();
        $this->commander = $this->createCommander();
        $this->config = $this->createConfig();

        $this->container->set([App::class, IApp::class], $this);
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
    public function start() {
        if ($this->started) {
            return;
        }

        $this->started = true;

        $this->kernel->initialize();
        $this->eventer->dispatch(new KernelInitializedEvent($this->kernel));
        $this->kernel->boot();
        $this->eventer->dispatch(new KernelBootedEvent($this->kernel));
        $this->eventer->dispatch(new AppStartedEvent($this));
    }

    /**
     * Shutdown App.
     */
    public function shutdown() {
        if ( ! $this->started) {
            return;
        }

        $this->started = false;

        $this->kernel->shutdown();
        $this->eventer->dispatch(new KernelShutdownEvent($this->kernel));
        $this->eventer->dispatch(new AppShutdownEvent($this));
    }

    /**
     * Load config or extend currently loaded config with
     * new one, based on a config array, IConfig or a config path.
     *
     * @param array|string|IConfig $config
     *
     * @return IConfig
     */
    public function loadConfig($config) {
        if (is_array($config)) {
            // load config from on an array of config paths
            if (array_is_indexed($config)) {
                $configLoader = $this->createConfigLoader();
                $configLoader->addPaths($config);
                $newConfig = $configLoader->load();

                $this->config->extend($newConfig);
            }
            // load config from an array of key => values
            else {
                $this->config->merge($config);
            }
        }
        // load config from a config path
        else if (is_string($config)) {
            $configLoader = $this->createConfigLoader();
            $configLoader->addPath($config);
            $newConfig = $configLoader->load();

            $this->config->extend($newConfig);
        } else if ($config instanceof IConfig) {
            $this->config->extend($config);
        }

        $this->eventer->dispatch(new ConfigLoadedEvent($this->config));

        return $this->config;
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
     * @return ICommander
     */
    public function getCommander() {
        return $this->commander;
    }

    /**
     * @return IConfig
     */
    public function getConfig() {
        return $this->config;
    }

    /**
     * @return string
     */
    public function getEnvironment() {
        return $this->environment;
    }

    /**
     * @param string $environment
     */
    public function setEnvironment($environment) {
        $this->environment = $environment;
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
     * @return ICommander
     */
    protected function createCommander() {
        $commander = new ContainerAwareCommander($this->container);
        $this->container->set([Commander::class, ICommander::class], $commander);

        return $commander;
    }

    /**
     * @return IConfigLoader
     */
    protected function createConfigLoader() {
        return new ConfigLoader($this->getEnvironment());
    }

    /**
     * @return Config
     */
    protected function createConfig() {
        $config = new Config();
        $this->container->set([Config::class, IConfig::class], $config);

        return $config;
    }
}
