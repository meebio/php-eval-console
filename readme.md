PHP Eval Console
================

In-browser, standalone console that executes your PHP code and returns the produced output. This package is based on package [darsain/laravel-console](https://github.com/darsain/laravel-console).

## Table of contents

* [Installation](#installation)
* [Configuration](#configuration)
* [Evaluators](#evaluators)
* [Authorizers](#authorizers)

## Installation

* Get package through composer:

```bash
composer require meebio/php-eval-console
```

* Copy `vendor/meebio/php-eval-console/assets` to `assets` in project root directory.

* Create `index.php` in project root directory with following content:
 
```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use Meebio\PhpEvalConsole\Console;

$console = new Console();
$console->boot();
```

* Open index.php page in browser.

## Configuration
## Evaluators
## Authorizers