<?php

namespace Tests\Weew\App;

use PHPUnit_Framework_TestCase;
use RuntimeException;
use Tests\Weew\App\Mocks\FakeApp;
use Weew\App\App;
use Weew\App\Events\AppShutdownEvent;
use Weew\App\Events\AppStartedEvent;
use Weew\App\Events\ConfigLoadedEvent;
use Weew\App\Events\KernelBootedEvent;
use Weew\App\Events\KernelInitializedEvent;
use Weew\App\Events\KernelShutdownEvent;
use Tests\Weew\App\Util\EventerTester;
use Weew\Commander\Commander;
use Weew\Commander\ICommander;
use Weew\Config\Config;
use Weew\Config\IConfig;
use Weew\Config\IConfigLoader;
use Weew\Container\IContainer;
use Weew\Eventer\Eventer;
use Weew\Eventer\IEventer;
use Weew\Kernel\IKernel;
use Weew\Kernel\Kernel;

class AppTest extends PHPUnit_Framework_TestCase {
    public function test_create() {
        $app = new App();
        $this->assertEquals('dev', $app->getEnvironment());
    }

    public function test_get_and_set_environment() {
        $app = new App();
        $this->assertEquals('dev', $app->getEnvironment());
        $app->setEnvironment('dev');
        $this->assertEquals('dev', $app->getEnvironment());
        $app = new App('test');
        $this->assertEquals('test', $app->getEnvironment());
    }

    public function test_get_container() {
        $app = new App();
        $this->assertTrue($app->getContainer() instanceof IContainer);
    }

    public function test_get_config() {
        $app = new App();
        $app->start();
        $config = $app->getConfig();
        $this->assertTrue($config instanceof IConfig);
        $sameConfig = $app->getContainer()->get(IConfig::class);
        $this->assertTrue($sameConfig === $config);
        $sameConfig = $app->getContainer()->get(Config::class);
        $this->assertTrue($sameConfig === $config);
    }

    public function test_get_kernel() {
        $app = new App();
        $kernel = $app->getKernel();

        $this->assertTrue($kernel instanceof IKernel);
        $sameKernel = $app->getContainer()->get(IKernel::class);
        $this->assertTrue($kernel === $sameKernel);
        $sameKernel = $app->getContainer()->get(Kernel::class);
        $this->assertTrue($kernel === $sameKernel);
    }

    public function test_get_eventer() {
        $app = new App();
        $eventer = $app->getEventer();

        $this->assertTrue($eventer instanceof IEventer);
        $sameEventer = $app->getContainer()->get(IEventer::class);
        $this->assertTrue($eventer === $sameEventer);
        $sameEventer = $app->getContainer()->get(Eventer::class);
        $this->assertTrue($eventer === $sameEventer);
    }

    public function test_get_commander() {
        $app = new App();
        $commander = $app->getCommander();

        $this->assertTrue($commander instanceof ICommander);
        $sameCommander = $app->getContainer()->get(ICommander::class);
        $this->assertTrue($commander === $sameCommander);
        $sameCommander = $app->getContainer()->get(Commander::class);
        $this->assertTrue($commander === $sameCommander);
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

        $app->getConfigLoader()->addConfig([]);
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

        $app->getConfigLoader()->addConfig([]);

        $app->start();
        $app->start();
        $app->start();

        $app->shutdown();
        $app->shutdown();
        $app->shutdown();

        $tester->assert();
    }

    public function test_it_returns_config_loader() {
        $app = new App();
        $this->assertTrue($app->getConfigLoader() instanceof IConfigLoader);
    }

    public function test_config_loader_has_the_same_environment_as_app() {
        $app = new App();
        $app->setEnvironment('env');
        $app->start();
        $this->assertEquals($app->getEnvironment(), $app->getConfigLoader()->getEnvironment());
    }

    public function test_it_reuses_the_same_config_loader() {
        $app = new App();
        $configLoader = $app->getConfigLoader();
        $app->setEnvironment('env');
        $this->assertTrue($app->getConfigLoader() === $configLoader);
    }

    public function test_it_adds_custom_environments() {
        $app = new App();
        $this->assertNull(
            $app->getConfigLoader()->getEnvironmentDetector()->detectEnvironment('foo_te')
        );
        $app->addEnvironment('test', ['te', 'st']);
        $this->assertEquals(
            'test',
            $app->getConfigLoader()->getEnvironmentDetector()->detectEnvironment('foo_te')
        );
    }

    public function test_multiple_start() {
        $app = new App();
        $app->start();
        $app->start();
    }

    public function test_get_config_before_start_throws_an_error() {
        $app = new App();
        $this->setExpectedException(RuntimeException::class);
        $app->getConfig();
    }

    public function test_get_config_after_app_start() {
        $app = new App();
        $app->start();
        $this->assertTrue($app->getConfig() instanceof IConfig);
    }

    public function test_get_and_set_debug() {
        $app = new App();
        $this->assertFalse($app->getDebug());
        $app->setDebug(true);
        $this->assertTrue($app->getDebug());
        $app = new App(null, true);
        $this->assertTrue($app->getDebug());
    }

    public function test_set_environment_after_app_start_throws_an_error() {
        $app = new App();
        $app->start();
        $this->setExpectedException(RuntimeException::class);
        $app->setEnvironment('test');
    }

    public function test_boot_multiple_times() {
        $app = new FakeApp();
        $app->boot();
        $app->boot();
    }

    public function test_environment_is_propagated_to_config() {
        $app = new App('foo');
        $app->start();
        $this->assertEquals('foo', $app->getConfig()->get('env'));
    }

    public function test_debug_is_propagated_to_config() {
        $app = new App(null, true);
        $app->start();
        $this->assertEquals(true, $app->getConfig()->get('debug'));
    }
}
