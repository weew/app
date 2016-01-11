<?php

namespace Tests\Weew\App\Events\Kernel;

use PHPUnit_Framework_TestCase;
use Weew\App\Events\Kernel\KernelShutdownEvent;
use Weew\Kernel\Kernel;

class KernelShutdownEventTest extends PHPUnit_Framework_TestCase {
    public function test_getters_and_setters() {
        $kernel = new Kernel();
        $event = new KernelShutdownEvent($kernel);
        $this->assertTrue($event->getKernel() === $kernel);
    }
}
