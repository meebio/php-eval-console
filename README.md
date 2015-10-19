PHP Eval Console
================

In-browser, standalone console that executes your PHP code and returns the produced output. This package is based on package [darsain/laravel-console](https://github.com/darsain/laravel-console).

![Screenshot](http://i.imgur.com/ryXpkvc.png)

## Table of contents

* [Installation](#installation)
* [Configuration](#configuration)
* [Evaluators](#evaluators)
* [Authorizers](#authorizers)
* [Queries callback](#queries-callback)

## Installation

Get package through composer:

```bash
composer require meebio/php-eval-console
```

Copy `vendor/meebio/php-eval-console/assets` to `assets` in project root directory.

Create `index.php` in project root directory with following content:

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use Meebio\PhpEvalConsole\Console;

$console = new Console();
$console->boot();
```

Open index.php page in browser.

## Configuration

Key | Type | Description | Default
--- | --- | --- | ---
assets_dir | string | Relative path to assets directory. | `'assets'`
views_path | string | Absolute path to views directory. Modify only if you want to use custom views. | `'<...>/Views'`
console_view_path | string | Absolute path to main view. Modify only if you want to use custom views. | `'<...>/Views/console.php'`
execute_url | string\|null | URL to application execute route. If null set it is guessed. | null
evaluator | `EvaluatorInterface` | Instance of EvaluatorInterface implementation that will be used for running code. | `EvalEvaluator`
authorizer  | null\|`AuthorizerInterface` | Instance of AuthorizerInterface implementation that will be used for running code. This option also accepts null or array of authorizers. | `IpAuthorizer`
queries_callback | null\|closure | Callback that will return database queries to display after running code. | null
post_execute_callback | null\|closure | Post execute callback. Can be used to perform some actions after code evaluation, but before returning result. | null
     *
## Evaluators

#### EvalEvaluator

Basic evaluator that uses `eval` command. This is entirely not secure.

#### PhpSandboxEvaluator

More advanced php evaluator that make use of [fieryprophet/php-sandbox](https://github.com/fieryprophet/php-sandbox) package. This sandbox class utilizes [PHP-Parser](https://github.com/nikic/PHP-Parser) to prevent sandboxed code from running unsafe code. If configured properly this evaluator could probably allow application to be exposed to public users. To use this evaluator require `fieryprophet/php-sandbox` package in composer.

## Authorizers

#### IpAuthorizer

This authorizer ensures that only access from provided ips is possible. Authorizer constructor takes to arguments first is array of allowed ips (null if this check should be disabled) and second is array of disallowed ips (null if this check should be disabled).

## Queries callback

Closure that will provide array of queries after code evaluation. Returned data should be in following format:

```php
array(
    array(
        'query' => 'SELECT * FROM `users`;',
        'time'  => 0.113,
    ),
    array(
        'query' => 'SELECT * FROM `posts` JOIN `category` ON `post`.`category_id` = `category`.`id`;',
        'time'  => 0.231,
    ),
);
```

## TODO

- Improve errors handling.
- Create Laravel 5 provider for easier integration.
