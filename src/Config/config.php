<?php

return array(
    'views_path'        => __DIR__ . '/../Views',
    'console_view_path' => __DIR__ . '/../Views/console.php',
    'execute_url'       => null,
    'evaluator'         => new \Meebio\PhpEvalConsole\Evaluators\EvalEvaluator(),
);
