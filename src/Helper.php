<?php

namespace Meebio\PhpEvalConsole;

class Helper
{

    /**
     * @var string
     */
    public static $templateDefaultDir;

    /**
     * Micro template evaluation function
     *
     * @param string $templateFile
     * @param array $vars
     * @return string
     */
    public static function template($templateFile, $vars = array())
    {
        $possiblePaths = array(
            $templateFile,
            $templateFile . '.php',
        );

        if (!is_null(static::$templateDefaultDir)) {
            $possiblePaths[] = static::$templateDefaultDir . '/' . $templateFile;
            $possiblePaths[] = static::$templateDefaultDir . '/' . $templateFile . '.php';
        }

        $templateFilePath = null;

        foreach ($possiblePaths as $path) {
            if (is_readable($path)) {
                $templateFilePath = $path;
                break;
            }
        }

        if (is_null($templateFilePath)) {
            throw new \InvalidArgumentException('Template file not exists or not readable');
        }

        extract($vars);

        ob_start();
        require($templateFilePath);
        $content = ob_get_contents();
        ob_end_clean();

        return $content;
    }
}
