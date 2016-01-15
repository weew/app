<?php

namespace Weew\App;

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
     * @return IConfigLoader
     */
    function getConfigLoader();

    /**
     * @return IConfig
     */
    function getConfig();

    /**
     * @return IConfig
     */
    function loadConfig();
}
