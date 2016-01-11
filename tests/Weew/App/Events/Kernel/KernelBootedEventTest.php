<?php

namespace Tests\Weew\App\Events\Kernel;

use PHPUnit_Framework_TestCase;
use Weew\App\Events\Kernel\KernelBootedEvent;
use Weew\Kernel\Kernel;

class KernelBootedEventTest extends PHPUnit_Framework_TestCase {
    public function test_getters_and_setters() {
        $kernel = new Kernel();
        $event = new KernelBootedEvent($kernel);
        $this->assertTrue($event->getKernel() === $kernel);
    }
}
