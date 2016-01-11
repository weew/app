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

## Installation

`composer require weew/php-app`

## Introduction

This package is meant to be used as a bearbone for any php applications. It uses the [weew/php-container](https://github.com/weew/php-container) package for dependency injection, [weew/php-kernel](https://github.com/weew/php-kernel) as a kernel where you can register your providers and the [weew/php-eventer](https://github.com/weew/php-eventer) package for event handing.

## Usage

Creating a new app is very simple:

```php
$app = new App();

// get depdency injection container
$app->getContainer();

// get kernel
$app->getKernel();

// get event bus
$app->getEventer();
```
