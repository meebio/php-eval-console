<?php

namespace Meebio\PhpEvalConsole\Evaluators;

interface EvaluatorInterface
{

    /**
     * @param string $code
     * @return bool
     */
    public function evaluate($code);

    /**
     * @return array
     */
    public function getErrors();

    /**
     * @return mixed
     */
    public function getOutput();

    /**
     * @return float
     */
    public function getExecutionTime();
}
