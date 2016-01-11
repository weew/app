<?php

namespace Tests\Weew\App\Events;

use PHPUnit_Framework_TestCase;
use Weew\App\Events\KernelBootedEvent;
use Weew\Kernel\Kernel;

class KernelBootedEventTest extends PHPUnit_Framework_TestCase {
    public function test_getters_and_setters() {
        $kernel = new Kernel();
        $event = new KernelBootedEvent($kernel);
        $this->assertTrue($event->getKernel() === $kernel);
    }
}
