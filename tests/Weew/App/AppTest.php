<?php

namespace Tests\Weew\App;

use PHPUnit_Framework_TestCase;
use Weew\App\Util\EventerTester;
use Weew\Container\IContainer;
use Weew\Eventer\IEventer;
use Weew\App\App;
use Weew\App\Events\App\AppShutdownEvent;
use Weew\App\Events\App\AppStartedEvent;
use Weew\App\Events\Kernel\KernelBootedEvent;
use Weew\App\Events\Kernel\KernelInitializedEvent;
use Weew\App\Events\Kernel\KernelShutdownEvent;
use Weew\Kernel\IKernel;

class AppTest extends PHPUnit_Framework_TestCase {
    public function test_create() {
        new App();
    }

    public function test_get_container() {
        $app = new App();
        $this->assertTrue($app->getContainer() instanceof IContainer);
    }

    public function test_get_kernel() {
        $app = new App();
        $this->assertTrue($app->getKernel() instanceof IKernel);
    }

    public function test_get_eventer() {
        $app = new App();
        $this->assertTrue($app->getEventer() instanceof IEventer);
    }

    public function test_start_and_shutdown_events() {
        $app = new App();

        $tester = new EventerTester($app->getEventer());
        $tester->setExpectedEvents([
            KernelInitializedEvent::class,
            KernelBootedEvent::class,
            AppStartedEvent::class,
            KernelShutdownEvent::class,
            AppShutdownEvent::class,
        ]);

        $app->run();
        $tester->assert();
    }
}
