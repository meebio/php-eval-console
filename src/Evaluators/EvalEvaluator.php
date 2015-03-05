<?php

namespace Meebio\PhpEvalConsole\Evaluators;

class EvalEvaluator implements EvaluatorInterface
{
    /**
     * @var mixed
     */
    protected $output;

    /**
     * @var array
     */
    protected $errors = array();

    /**
     * @var float
     */
    protected $executionTime = 0;

    protected function reset()
    {
        $this->output        = null;
        $this->errors        = array();
        $this->executionTime = 0;
    }

    /**
     * @param string $code
     * @return bool
     */
    public function evaluate($code)
    {
        $this->reset();

        try {
            ob_start();

            $executionStart = microtime(true);
            $success        = @eval($code);
            $executionEnd   = microtime(true);

            $this->executionTime = $executionEnd - $executionStart;
            $this->output        = ob_get_contents();

            ob_end_clean();

            if (!is_null(error_get_last())) {
                $this->addError(error_get_last());
            }
        } catch (\Exception $e) {
            $this->addError(
                array(
                    'type'    => get_class($e),
                    'message' => $e->getMessage(),
                    'file'    => $e->getFile(),
                    'line'    => $e->getLine(),
                )
            );
            $success = false;
        }

        return $success;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @return mixed
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * @return float
     */
    public function getExecutionTime()
    {
        return $this->executionTime;
    }

    protected function addError($error)
    {
        $this->errors[] = $error;
    }
}
