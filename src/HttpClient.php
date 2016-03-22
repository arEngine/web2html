<?php namespace Engine;

use GuzzleHttp\Client as GuzzleClient;

/**
 * Class HttpClient
 *
 * @author  Adriano Rosa <http://adrianorosa.com>
 * @date    19/03/16 23:07
 *
 * @package Engine
 */
class HttpClient
{
    protected static $client;

    public static function getClient()
    {
        if ( static::$client ) {
            return static::$client;
        }

        return static::$client = new GuzzleClient([
            'base_uri' => Domain::baseUrl(),
            'timeout'  => Config::get('timeout', 10),
            'allow_redirects' => Config::get('follow-redirects'),
        ]);
    }
}
