<?php

namespace Meebio\PhpEvalConsole;

class Console
{

    /**
     * @var array
     */
    protected $config;

    /**
     * @var float
     */
    protected $consoleStart;

    /**
     * Array with error code => string pairs.
     *
     * Used to convert error codes into human readable strings.
     *
     * @var array
     */
    protected static $error_map = array(
        E_ERROR             => 'E_ERROR',
        E_WARNING           => 'E_WARNING',
        E_PARSE             => 'E_PARSE',
        E_NOTICE            => 'E_NOTICE',
        E_CORE_ERROR        => 'E_CORE_ERROR',
        E_CORE_WARNING      => 'E_CORE_WARNING',
        E_COMPILE_ERROR     => 'E_COMPILE_ERROR',
        E_COMPILE_WARNING   => 'E_COMPILE_WARNING',
        E_USER_ERROR        => 'E_USER_ERROR',
        E_USER_WARNING      => 'E_USER_WARNING',
        E_USER_NOTICE       => 'E_USER_NOTICE',
        E_STRICT            => 'E_STRICT',
        E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
        E_DEPRECATED        => 'E_DEPRECATED',
        E_USER_DEPRECATED   => 'E_USER_DEPRECATED',
        E_ALL               => 'E_ALL',
    );

    /**
     * Execution profile.
     *
     * @var array
     */
    protected $profile = array(
        'queries'      => array(),
        'memory'       => 0,
        'memory_peak'  => 0,
        'time'         => 0,
        'time_queries' => 0,
        'time_total'   => 0,
        'output'       => '',
        'output_size'  => 0,
        'error'        => false
    );

    /**
     * @param array $config
     */
    public function __construct($config = array())
    {
        $this->consoleStart = microtime(true);
        $this->loadConfig($config);
        Helper::$templateDefaultDir = $this->getConfigItem('viewsPath');
    }

    protected function loadConfig($config = array())
    {
        $defaultConfigPath = __DIR__ . '/Config/config.php';
        $defaultConfig = require $defaultConfigPath;
        $this->config = array_merge($defaultConfig, $config);

        if (is_null($this->config['executeUrl'])) {
            $this->config['executeUrl'] = $this->detectExecuteUrl();
        }
    }

    protected function getConfig()
    {
        return $this->config;
    }

    protected function getConfigItem($key)
    {
        return array_key_exists($key, $this->config) ? $this->config[$key] : null;
    }

    public function boot($action = null)
    {
        if (is_null($action)) {
            if ($this->detectMethod() === 'post') {
                $this->execute();
            } else {
                $this->renderView();
            }
        } elseif ($action === 'console') {
            $this->renderView();
        } elseif ($action === 'execute') {
            $this->execute();
        }
    }

    protected function detectMethod()
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    protected function detectExecuteUrl()
    {
        return 'http'.(empty($_SERVER['HTTPS'])?'':'s').'://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
    }

    protected function getInput()
    {
        if (!isset($_POST['code']) || !is_string($_POST['code'])) {
            throw new \Exception('Code not sent or invalid');
        }

        return $_POST['code'];
    }

    protected function renderView()
    {
        $consoleViewPath = $this->getConfigItem('consoleViewPath');

        echo Helper::template($consoleViewPath, array(
            'config' => $this->getConfig(),
        ));
    }

    protected function returnJson($data)
    {
        echo json_encode($data);
        exit;
    }

    /**
     * Executes a code and returns current profile.
     */
    protected function execute()
    {
        $code = $this->getInput();

        // Execute the code
        ob_start();
        $console_execute_start = microtime(true);
        $estatus               = @eval($code);
        $console_execute_end   = microtime(true);
        $output                = ob_get_contents();
        ob_end_clean();

        // When error occurred, add it to profile.
        if ($estatus === false) {
            $this->addProfile('error', error_get_last());
        }

        // Extend the profile
        $this->addProfile(
            array(
                'time'        => round(($console_execute_end - $console_execute_start) * 1000),
                'output'      => $output,
                'output_size' => strlen($output)
            )
        );

        $this->returnJson($this->getProfile());
    }

    /**
     * Adds one or multiple fields into profile.
     *
     * @param mixed $property Property name, or an array of name => value pairs.
     * @param mixed $value Property value.
     */
    public function addProfile($property, $value = null)
    {
        if (gettype($property) === 'array') {
            foreach ($property as $key => $value) {
                $this->addProfile($key, $value);
            }
            return;
        }

        // Normalize properties
        $normalizer_name = 'normalize' . ucfirst($property);
        if (method_exists($this, $normalizer_name)) {
            $value = call_user_func(array($this, $normalizer_name), $value);
        }

        $this->profile[$property] = $value;
    }

    /**
     * Returns current profile.
     *
     * @return array
     */
    public function getProfile()
    {
        // Total execution time by queries
        $time_queries = 0;
        foreach ($this->profile['queries'] as $query) {
            $time_queries += $query['time'];
        }

        // Extend the profile with current data
        static::addProfile(
            array(
                'memory'       => memory_get_usage(true),
                'memory_peak'  => memory_get_peak_usage(true),
                'time_queries' => round($time_queries),
                'time_total'   => round((microtime(true) - $this->consoleStart) * 1000),
            )
        );

        return $this->profile;
    }

    /**
     * Normalizes error profile.
     *
     * @param mixed $error Error object or array.
     * @return array Normalized error array.
     */
    public function normalizeError($error)
    {
        // Set human readable error type
        if (isset($error['type']) and isset(static::$error_map[$error['type']])) {
            $error['type'] = static::$error_map[$error['type']];
        }

        // Validate and return the error
        if (isset($error['type'], $error['message'], $error['file'], $error['line'])) {
            return $error;
        } else {
            return $this->profile['error'];
        }
    }

    ///**
    // * Processes a Laravel query event to profile executed queries.
    // *
    // * @param  string $sql
    // * @param  array $bindings
    // * @param  int $time
    // */
    //public static function query($sql, $bindings, $time)
    //{
    //    foreach ($bindings as $binding) {
    //        // Sometimes, object $binding is passed, and needs to be stringified
    //        if (gettype($binding) == 'object') {
    //            $class_name = get_class($binding);
    //            switch ($class_name) {
    //                case 'DateTime':
    //                    $binding = $binding->format('Y-m-d H:i:s e');
    //                    break;
    //
    //                default:
    //                    $binding = '(object)' . $class_name;
    //            }
    //        }
    //
    //        $binding = DB::connection()->getPdo()->quote($binding);
    //
    //        $sql = preg_replace('/\?/', $binding, $sql, 1);
    //        $sql = htmlspecialchars(htmlspecialchars_decode($sql));
    //    }
    //
    //    static::$profile['queries'][] = array(
    //        'query' => $sql,
    //        'time'  => $time
    //    );
    //}

    ///**
    // * Attaches Laravel event listeners.
    // */
    //public static function attach()
    //{
    //    Event::listen(
    //        'illuminate.query',
    //        function ($sql, $bindings, $time) {
    //            Console::query($sql, $bindings, $time);
    //        }
    //    );
    //}
}
