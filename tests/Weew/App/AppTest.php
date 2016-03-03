<?php

namespace Tests\Weew\App;

use PHPUnit_Framework_TestCase;
use Weew\App\App;
use Weew\App\Events\AppShutdownEvent;
use Weew\App\Events\AppStartedEvent;
use Weew\App\Events\ConfigLoadedEvent;
use Weew\App\Events\KernelBootedEvent;
use Weew\App\Events\KernelInitializedEvent;
use Weew\App\Events\KernelShutdownEvent;
use Weew\App\Exceptions\ConfigNotLoadedException;
use Weew\App\Util\EventerTester;
use Weew\Commander\ICommander;
use Weew\Config\IConfig;
use Weew\Config\IConfigLoader;
use Weew\Container\IContainer;
use Weew\Eventer\IEventer;
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
        $kernel = $app->getKernel();

        $this->assertTrue($kernel instanceof IKernel);
        $sameKernel = $app->getContainer()->get(IKernel::class);
        $this->assertTrue($kernel === $sameKernel);
    }

    public function test_get_eventer() {
        $app = new App();
        $eventer = $app->getEventer();

        $this->assertTrue($eventer instanceof IEventer);
        $sameEventer = $app->getContainer()->get(IEventer::class);
        $this->assertTrue($eventer === $sameEventer);
    }

    public function test_get_and_commander() {
        $app = new App();
        $commander = $app->getCommander();

        $this->assertTrue($commander instanceof ICommander);
        $sameCommander = $app->getContainer()->get(ICommander::class);
        $this->assertTrue($commander === $sameCommander);
    }

    public function test_get_config_loader() {
        $app = new App();
        $this->assertTrue($app->getConfigLoader() instanceof IConfigLoader);
    }

    public function test_config_loaded() {
        $app = new App();
        $app->run();
        $config = $app->getConfig();

        $this->assertTrue($config instanceof IConfig);

        $sameConfig = $app->getContainer()->get(IConfig::class);
        $this->assertTrue($config === $sameConfig);
    }

    public function test_load_config_returns_config() {
        $app = new App();
        $this->assertTrue($app->loadConfig() instanceof IConfig);
    }

    public function test_get_config_before_app_was_started_throws_exception() {
        $app = new App();
        $this->setExpectedException(ConfigNotLoadedException::class);
        $app->getConfig();
    }

    public function test_load_config_before_app_is_started() {
        $app = new App();
        $tester = new EventerTester($app->getEventer());
        $tester->setExpectedEvents([ConfigLoadedEvent::class]);
        $app->loadConfig();

        $tester->assert();

        $tester = new EventerTester($app->getEventer());
        $tester->setExpectedEvents([ConfigLoadedEvent::class]);
        $app->run();

        $tester->assert();
    }

    public function test_start_and_shutdown_events() {
        $app = new App();

        $tester = new EventerTester($app->getEventer());
        $tester->setExpectedEvents([
            ConfigLoadedEvent::class,
            KernelInitializedEvent::class,
            KernelBootedEvent::class,
            AppStartedEvent::class,
            KernelShutdownEvent::class,
            AppShutdownEvent::class,
        ]);

        $app->run();
        $tester->assert();
    }

    public function test_prevents_multiple_starts_and_shutdowns() {
        $app = new App();

        $tester = new EventerTester($app->getEventer());
        $tester->setExpectedEvents([
            ConfigLoadedEvent::class,
            KernelInitializedEvent::class,
            KernelBootedEvent::class,
            AppStartedEvent::class,
            KernelShutdownEvent::class,
            AppShutdownEvent::class,
        ]);

        $app->start();
        $app->start();
        $app->start();

        $app->shutdown();
        $app->shutdown();
        $app->shutdown();

        $tester->assert();
    }
}
