<?php

namespace Tests\Weew\App\Events\App;

use PHPUnit_Framework_TestCase;
use Weew\App\App;
use Weew\App\Events\App\AppShutdownEvent;

class AppShutdownEventTest extends PHPUnit_Framework_TestCase {
    public function test_getters_and_setters() {
        $app = new App();
        $event = new AppShutdownEvent($app);
        $this->assertTrue($event->getApp() === $app);
    }
}
