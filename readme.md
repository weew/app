# App

[![Build Status](https://img.shields.io/travis/weew/php-app.svg)](https://travis-ci.org/weew/php-app)
[![Code Quality](https://img.shields.io/scrutinizer/g/weew/php-app.svg)](https://scrutinizer-ci.com/g/weew/php-app)
[![Test Coverage](https://img.shields.io/coveralls/weew/php-app.svg)](https://coveralls.io/github/weew/php-app)
[![Version](https://img.shields.io/packagist/v/weew/php-app.svg)](https://packagist.org/packages/weew/php-app)
[![Licence](https://img.shields.io/packagist/l/weew/php-app.svg)](https://packagist.org/packages/weew/php-app)

## Table of contents

- [Installation](#installation)
- [Introduction](#introduction)
- [Usage](#usage)
- [Extensions](#extensions)

## Installation

`composer require weew/php-app`

## Introduction

This package is meant to be used as a barebone for any php applications. It uses the [weew/php-container](https://github.com/weew/php-container) package for dependency injection, [weew/php-kernel](https://github.com/weew/php-kernel) as a kernel where you can register your providers, the [weew/php-eventer](https://github.com/weew/php-eventer) package for event handing and the [weew/php-commander](https://github.com/weew/php-commander) package as the command bus. Configuration is handled by the [weew/php-config](https://github.com/weew/php-config) package.

Please read documentation of different components to see how they work.

## Usage

Creating a new app is very simple:

```php
$app = new App('environment');

// or

$app = new App();
$app->setEnvironment('environment');

// get depdency injection container
$app->getContainer();

// get kernel
$app->getKernel();

// get event bus
$app->getEventer();

// get command bus
$app->getCommander();
```

You can provide config sources using the config loader.

```php
// get config
$app->getConfigLoader()
    ->addPath('/path/to/config')
    ->addRuntimeConfig(['some' => 'value']);
```

Be aware that applications current `environment` and `debug` mode will always be available inside the config object.

```php
// "dev" by default
$app->getEnvironment();
$app->setEnvironment('test');
// will be set to test
$app->getConfig()->get('env');

// false by default
$app->getDebug();
$app->setDebug(true);
// will be set to true
$app->getConfig()->get('debug');
```

Be aware that config is only available after the application start.

## Extensions

There are several extensions available:

- [weew/php-app-doctrine](https://github.com/weew/php-app-doctrine)
- [weew/php-app-monolog](https://github.com/weew/php-app-monolog)
- [weew/php-app-twig](https://github.com/weew/php-app-twig)
- [weew/php-app-http](https://github.com/weew/php-app-http)
- [weew/php-app-http-request-handler](https://github.com/weew/php-app-http-request-handler)
- [weew/php-app-error-handler](https://github.com/weew/php-app-error-handler)
- [weew/php-app-error-handler-bugsnag](https://github.com/weew/php-app-error-handler-bugsnag)
- [weew/php-app-error-handler-monolog](https://github.com/weew/php-app-error-handler-monolog)

