<?php

namespace Weew\App\Events;

use Weew\App\IApp;
use Weew\Eventer\Event;

class AppShutdownEvent extends Event {
    /**
     * @var IApp
     */
    private $app;

    /**
     * AppShutdownEvent constructor.
     *
     * @param IApp $app
     */
    public function __construct(IApp $app) {
        $this->app = $app;
    }

    /**
     * @return IApp
     */
    public function getApp() {
        return $this->app;
    }
}
