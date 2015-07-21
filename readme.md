PHP Eval Console
================

In-browser, standalone console that executes your PHP code and returns the produced output. This package is based on package [darsain/laravel-console](https://github.com/darsain/laravel-console).

![Screenshot](http://i.imgur.com/ryXpkvc.png)

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

Key | Type | Description | Default
--- | --- | --- | ---
assets_dir | string | Relative path to assets directory. | `'assets'`
views_path | string | Absolute path to views directory. Modify only if you want to use custom views. | `'<...>/Views'`
console_view_path | string | Absolute path to main view. Modify only if you want to use custom views. | `'<...>/Views/console.php'`
execute_url | string\|null | URL to application execute route. If null set it is guessed. | null
evaluator | `EvaluatorInterface` | Instance of EvaluatorInterface implementation that will be used for running code. | `EvalEvaluator`
authorizer  | null\|`AuthorizerInterface` | Instance of AuthorizerInterface implementation that will be used for running code. This option also accepts null or array of authorizers. | `IpAuthorizer`
queries_callback | null\|callback | Callback that will return database queries to display after running code. | null

## Evaluators
## Authorizers