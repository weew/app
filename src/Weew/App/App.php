<?php

namespace Weew\App;

use RuntimeException;
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
    protected $booted = false;

    /**
     * @var bool
     */
    protected $started = false;

    /**
     * @var string
     */
    protected $environment;

    /**
     * @var bool
     */
    protected $debug;

    /**
     * @var IContainer
     */
    protected $container;

    /**
     * @var ContainerAwareKernel
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
     * @var IConfigLoader
     */
    protected $configLoader;

    /**
     * App constructor.
     *
     * @param string $environment
     * @param bool $debug
     */
    public function __construct($environment = null, $debug = null) {
        if ($environment === null) {
            $environment = $this->getDefaultEnvironment();
        }

        if ($debug === null) {
            $debug = $this->getDefaultDebug();
        }

        $this->setEnvironment($environment);
        $this->setDebug($debug);

        $this->container = new Container();
        $this->container->set([App::class, IApp::class], $this);

        $this->kernel = new ContainerAwareKernel($this->container);
        $this->container->set([Kernel::class, IKernel::class], $this->kernel);

        $this->eventer = new ContainerAwareEventer($this->container);
        $this->container->set([Eventer::class, IEventer::class], $this->eventer);

        $this->commander = new ContainerAwareCommander($this->container);
        $this->container->set([Commander::class, ICommander::class], $this->commander);

        $this->configLoader = new ConfigLoader();
    }

    /**
     * Stuff that needs to be done before application starts.
     */
    protected function boot() {
        if ($this->booted) {
            return;
        }

        $this->booted = true;

        $this->getConfigLoader()->setEnvironment($this->getEnvironment());
        $this->config = $this->getConfigLoader()->load();
        $this->container->set([Config::class, IConfig::class], $this->config);
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
    public function start() {
        if ($this->started) {
            return;
        }

        $this->started = true;

        $this->boot();
        $this->config->set('env', $this->getEnvironment());
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
        if ( ! $this->config instanceof IConfig) {
            throw new RuntimeException(
                'Config has not been loaded yet. ' .
                'Make sure application is started.'
            );
        }

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
        if ($this->started) {
            throw new RuntimeException(
                'Application has already been started ' .
                'an environment can not be changed.'
            );
        }

        $this->environment = $environment;
    }

    /**
     * @return bool
     */
    public function getDebug() {
        return $this->debug;
    }

    /**
     * @param bool $debug
     */
    public function setDebug($debug) {
        $this->debug = $debug;
    }

    /**
     * Add additional configuration environment.
     *
     * @param string $name
     * @param array $abbreviations
     */
    public function addEnvironment($name, array $abbreviations) {
        $this->getConfigLoader()
            ->getEnvironmentDetector()
            ->addEnvironmentRule($name, $abbreviations);
    }

    /**
     * @return IConfigLoader
     */
    public function getConfigLoader() {
        return $this->configLoader;
    }

    /**
     * @return string
     */
    protected function getDefaultEnvironment() {
        return 'dev';
    }

    /**
     * @return bool
     */
    protected function getDefaultDebug() {
        return false;
    }
}
