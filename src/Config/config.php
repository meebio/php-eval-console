<?php

return [
    'assets_url'        => '../public',
    'views_path'        => __DIR__ . '/../Views',
    'console_view_path' => __DIR__ . '/../Views/console.php',
    'execute_url'       => null,
    'evaluator'         => new Meebio\PhpEvalConsole\Evaluators\PhpSandboxEvaluator(),
    'queries_callback'  => null,
];
