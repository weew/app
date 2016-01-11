<?php

namespace Tests\Weew\App\Events\Kernel;

use PHPUnit_Framework_TestCase;
use Weew\App\Events\Kernel\KernelInitializedEvent;
use Weew\Kernel\Kernel;

class KernelInitializedEventTest extends PHPUnit_Framework_TestCase {
    public function test_getters_and_setters() {
        $kernel = new Kernel();
        $event = new KernelInitializedEvent($kernel);
        $this->assertTrue($event->getKernel() === $kernel);
    }
}
