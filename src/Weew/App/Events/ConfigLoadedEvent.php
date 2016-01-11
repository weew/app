<?php

namespace Weew\App\Events;

use Weew\Config\IConfig;
use Weew\Eventer\Event;

class ConfigLoadedEvent extends Event {
    /**
     * @var IConfig
     */
    protected $config;

    /**
     * ConfigLoadedEvent constructor.
     *
     * @param IConfig $config
     */
    public function __construct(IConfig $config) {
        $this->config = $config;
    }

    /**
     * @return IConfig
     */
    public function getConfig() {
        return $this->config;
    }
}
