<?php

namespace Weew\App\Events\App;

use Weew\Eventer\Event;
use Weew\App\IApp;

class AppStartedEvent extends Event {
    /**
     * @var IApp
     */
    private $app;

    /**
     * AppStartedEvent constructor.
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
