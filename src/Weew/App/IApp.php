<?php

namespace Weew\App;

use Weew\Commander\ICommander;
use Weew\Config\IConfig;
use Weew\Config\IConfigLoader;
use Weew\Container\IContainer;
use Weew\Eventer\IEventer;
use Weew\Kernel\IKernel;

interface IApp {
    /**
     * @return mixed
     */
    function run();

    /**
     * @return mixed
     */
    function start();

    /**
     * @return mixed
     */
    function shutdown();

    /**
     * Get dependency injection container instance.
     *
     * @return IContainer
     */
    function getContainer();

    /**
     * Get app kernel instance.
     *
     * @return IKernel
     */
    function getKernel();

    /**
     * Get event bus instance.
     *
     * @return IEventer
     */
    function getEventer();

    /**
     * @return ICommander
     */
    function getCommander();

    /**
     * @return IConfig
     */
    function getConfig();

    /**
     * @return IConfigLoader
     */
    function getConfigLoader();

    /**
     * @return string
     */
    function getEnvironment();

    /**
     * @param string $environment
     */
    function setEnvironment($environment);

    /**
     * @return bool
     */
    function getDebug();

    /**
     * @param bool $debug
     */
    function setDebug($debug);
}
