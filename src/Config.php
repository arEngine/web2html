<?php namespace Engine;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;

/**
 * Class Config
 *
 * @author  Adriano Rosa <http://adrianorosa.com>
 * @date    18/03/16 13:37
 *
 * @package Engine
 */
class Config
{
    const CRAWLER_LIMIT = 20000;

    const CONSOLE_VERBOSE = 1;

    protected static $config = [];

    /**
     * @param null $ymlFile
     *
     * @return mixed
     */
    public static function boot($ymlFile = null)
    {
        $config = [];
        $file = ( !empty($ymlFile) && file_exists($ymlFile)) ? $ymlFile : 'config.yml';

        // look for a user defined path to config file
        if ( is_readable($file)) {

            try {

                if ( ! is_null($parse = Yaml::parse(file_get_contents($file))) ) {
                    $config = $parse;
                };

            } catch (ParseException $e) {

                Console::error($e->getMessage());
                exit(1);
            }
        }

        return $config;
    }

    /**
     * @param $key
     * @param null $value
     * @return void
     */
    public static function set($key, $value = null)
    {
        if ( is_array($key)) {

            foreach ($key as $k => $v) {
                static::set($k, $v);
            }
            return;
        }

        if ( !isset(self::$config[$key]) ) {
            self::$config[$key] = $value;
        }
    }

    public static function get($key, $default = null)
    {
        return isset(self::$config[$key]) ? self::$config[$key] : $default;
    }

    public static function all()
    {
        return self::$config;
    }
}
