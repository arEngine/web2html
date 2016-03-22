<?php namespace Engine;

/**
 * Class Console
 *
 * @author  Adriano Rosa <http://adrianorosa.com>
 * @date    18/03/16 04:06
 *
 * @package Engine
 */
class Console
{
    public static function success($message)
    {
        self::setOutput('success', $message);
    }

    public static function info($message)
    {
        self::setOutput('info', $message);
    }

    public static function warn($message)
    {
        self::setOutput('warn', $message);
    }

    public static function text($message)
    {
        self::setOutput('text', $message);
    }

    public static function alert($message)
    {
        self::setOutput('alert', $message);
    }

    public static function error($message)
    {
        self::setOutput('error', $message);
    }

    public static function debug($message)
    {
        self::setOutput('debug', $message);
    }

    public static function setOutput($type, $string = null)
    {
        switch ($type) {
            case 'success':
                $message = sprintf("✔ %s", $string);
                $pretty = "\033[0;32m";
                $browser = '<span style="color:green">%s</span><br>';
                break;

            case 'info':

                $message = sprintf("⇾ %s", $string);
                //$pretty = "\033[0;33m";
                // "\033[0;34m"; // blue
                $pretty = "\033[0;36m"; // cyan
                $browser = '<span style="color:blue">%s</span><br>';
                break;

            case 'warn':

                $message = sprintf("⇾ %s", $string);
                $pretty = "\033[0;35m";
                $browser = '<span style="color:magenta">%s</span><br>';
                break;

            case 'alert':
                $message = sprintf("⇾ %s", $string);
                $pretty = "\033[0;33m";
                $browser = '<span style="color:#FFC107">%s</span><br>';
                break;


            case 'text':

                $message = sprintf("⇾ %s", $string);
                $pretty = "\033[1;30m";
                $browser = '<span style="color:gray">%s</span><br>';
                break;

            case 'debug':

                $message = sprintf("⇾ %s", $string);
                $pretty = "\033[1;30m";
                $browser = '<span style="color:gray">%s</span><br>';
                break;

            default:
                $message = sprintf("✗ Error %s", $string);
                $pretty = "\033[0;31m";
                $browser = '<span style="color:red">%s</span><br>';
        }

        $message = $pretty . $message . "\033[0m" . PHP_EOL;

        if ( $type === 'debug' || Config::CONSOLE_VERBOSE === 1 ) {
            if(!defined("STDIN")) {
                echo '<pre>';
                echo sprintf($browser, $string);
                echo '</pre>';
            } else {
                echo $message;
            }
        }

    }

    public static function header($type, $message)
    {
        self::setOutput($type, "---------------------------------------------------");
        self::setOutput($type, $message);
        self::setOutput($type, "---------------------------------------------------");
    }

    public static function sendOutput($name = null)
    {
        $output = '';

        $output .= "------------------------------". PHP_EOL;
        $output .= ' files has been created' . PHP_EOL;
        $output .= "------------------------------". PHP_EOL;
        echo $output;
    }
}
