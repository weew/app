# App

[![Build Status](https://img.shields.io/travis/weew/app.svg)](https://travis-ci.org/weew/app)
[![Code Quality](https://img.shields.io/scrutinizer/g/weew/app.svg)](https://scrutinizer-ci.com/g/weew/app)
[![Test Coverage](https://img.shields.io/coveralls/weew/app.svg)](https://coveralls.io/github/weew/app)
[![Version](https://img.shields.io/packagist/v/weew/app.svg)](https://packagist.org/packages/weew/app)
[![Licence](https://img.shields.io/packagist/l/weew/app.svg)](https://packagist.org/packages/weew/app)

## Table of contents

- [Installation](#installation)
- [Introduction](#introduction)
- [Usage](#usage)
- [Extensions](#extensions)

## Installation

`composer require weew/app`

## Introduction

This package is meant to be used as a barebone for any php applications. It uses the [weew/container](https://github.com/weew/container) package for dependency injection, [weew/kernel](https://github.com/weew/kernel) as a kernel where you can register your providers, the [weew/eventer](https://github.com/weew/eventer) package for event handing and the [weew/commander](https://github.com/weew/commander) package as the command bus. Configuration is handled by the [weew/config](https://github.com/weew/config) package.

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

- [weew/app-doctrine](https://github.com/weew/app-doctrine)
- [weew/app-monolog](https://github.com/weew/app-monolog)
- [weew/app-twig](https://github.com/weew/app-twig)
- [weew/app-http](https://github.com/weew/app-http)
- [weew/app-http-request-handler](https://github.com/weew/app-http-request-handler)
- [weew/app-error-handler](https://github.com/weew/app-error-handler)
- [weew/app-error-handler-bugsnag](https://github.com/weew/app-error-handler-bugsnag)
- [weew/app-error-handler-monolog](https://github.com/weew/app-error-handler-monolog)

