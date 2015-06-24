<?php

namespace Meebio\PhpEvalConsole\Evaluators;

use PHPSandbox\PHPSandbox;

class PhpSandboxEvaluator implements EvaluatorInterface
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

    /**
     * @var PHPSandbox
     */
    protected $sandbox;

    public function __construct(PHPSandbox $sandbox = null)
    {
        if (is_null($sandbox)) {
            $sandbox = new PHPSandbox;
        }

        $sandbox->capture_output = true;

        $this->sandbox = $sandbox;
    }

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

            $this->output        = $this->sandbox->execute($code);
            $this->executionTime = $this->sandbox->execution_time;
            $success             = true;

            if (!is_null($this->sandbox->get_last_error())) {
                $this->addError($this->sandbox->get_last_error());
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
