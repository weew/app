<?php

namespace Tests\Weew\App\Events;

use PHPUnit_Framework_TestCase;
use Weew\App\Events\ConfigLoadedEvent;
use Weew\Config\Config;

class ConfigLoadedEventTest extends PHPUnit_Framework_TestCase {
    public function test_getters_and_setters() {
        $config = new Config();
        $event = new ConfigLoadedEvent($config);
        $this->assertTrue($config === $event->getConfig());
    }
}
