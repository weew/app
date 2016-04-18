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
        $this->assertEquals('prod', $app->getEnvironment());
    }

    public function test_get_and_set_environment() {
        $app = new App('test');
        $this->assertEquals('test', $app->getEnvironment());
        $app->setEnvironment('dev');
        $this->assertEquals('dev', $app->getEnvironment());
    }

    public function test_get_container() {
        $app = new App();
        $this->assertTrue($app->getContainer() instanceof IContainer);
    }

    public function test_get_config() {
        $app = new App();
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

    public function test_load_config_from_path() {
        $app = new App();
        $config = $app->getConfig();
        $config->set('key', 'value');
        $app->loadConfig(__DIR__ . '/config/config1.php');
        $config = $app->loadConfig([__DIR__ . '/config/config2.php']);

        $this->assertTrue($config instanceof IConfig);
        $this->assertTrue($config === $app->getConfig());
        $this->assertTrue($app->getContainer()->get(IConfig::class) === $config);
        $this->assertEquals(['key' => 'value', 'foo' => 'bar', 'bar' => 'baz'], $config->toArray());
    }

    public function test_load_config_from_array_of_paths() {
        $app = new App();
        $app->getConfig()->set('key', 'value');
        $config = $app->loadConfig(__DIR__ . '/config');

        $this->assertTrue($config instanceof IConfig);
        $this->assertTrue($config === $app->getConfig());
        $this->assertEquals(['key' => 'value', 'foo' => 'bar', 'bar' => 'baz'], $config->toArray());
    }

    public function test_load_config_from_array() {
        $app = new App();
        $app->getConfig()->set('key', 'value');
        $config = $app->loadConfig(['yolo' => 'swag']);

        $this->assertTrue($config instanceof IConfig);
        $this->assertTrue($config === $app->getConfig());
        $this->assertEquals(['key' => 'value', 'yolo' => 'swag'], $config->toArray());
    }

    public function test_load_config_from_another_config() {
        $app = new App();
        $app->getConfig()->set('key', 'value');
        $config = $app->loadConfig(new Config(['yolo' => 'swag']));

        $this->assertTrue($config instanceof IConfig);
        $this->assertTrue($config === $app->getConfig());
        $this->assertEquals(['key' => 'value', 'yolo' => 'swag'], $config->toArray());
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

        $app->loadConfig([]);
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

        $app->loadConfig([]);

        $app->start();
        $app->start();
        $app->start();

        $app->shutdown();
        $app->shutdown();
        $app->shutdown();

        $tester->assert();
    }

    public function test_environment_switch_leads_to_config_reload() {
        $app = new App('dev');
        $config = $app->getConfig();
        $this->assertTrue($config instanceof IConfig);
        $app->setEnvironment('dev');
        $this->assertTrue($config === $app->getConfig());
        $app->setEnvironment('prod');
        $this->assertTrue($config !== $app->getConfig());
        $config = $app->getConfig();
        $this->assertTrue($config instanceof IConfig);
    }

    public function test_config_reload_reloads_config_sources() {
        $app = new App();
        $app->loadConfig(__DIR__ . '/config/config1.php');
        $app->loadConfig([__DIR__ . '/config/config2.php']);
        $config = $app->getConfig();
        $config->set('key', 'value');
        $this->assertEquals(['key' => 'value', 'foo' => 'bar', 'bar' => 'baz'], $config->toArray());
        $app->setEnvironment('dev');
        $this->assertFalse($config === $app->getConfig());
        $config = $app->getConfig();
        $this->assertTrue($app->getContainer()->get(IConfig::class) === $config);
        $this->assertEquals(['foo' => 'bar', 'bar' => 'baz'], $config->toArray());
    }

    public function test_it_returns_config_loader() {
        $app = new App();
        $this->assertTrue($app->getConfigLoader() instanceof IConfigLoader);
    }

    public function test_config_loader_has_the_same_environment_as_app() {
        $app = new App();
        $configLoader = $app->getConfigLoader();
        $this->assertEquals($app->getEnvironment(), $configLoader->getEnvironment());
        $app->setEnvironment('env');
        $this->assertEquals($app->getEnvironment(), $configLoader->getEnvironment());
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
}
