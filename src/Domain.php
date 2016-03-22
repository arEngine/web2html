<?php namespace Engine;
use League\Uri\Schemes\Http;

/**
 * Class Domain
 *
 * @author  Adriano Rosa <http://adrianorosa.com>
 * @date    19/03/16 10:41
 *
 * @package Engine
 */
class Domain
{
    /**
     * @var Http
     */
    protected $url;

    protected static $baseurl;

    /**
     * @param $value
     *
     * @return Http
     */
    public static function setBaseUrl($value)
    {
        $domain = $value;

        if ( !preg_match(',^https?\:\/\/,', $domain) ) {
            $domain = 'http://'.$domain;
        }

        $httpUri = Http::createFromString($domain);

        self::$baseurl = $httpUri->getScheme().'://'.$httpUri->getAuthority();

        Console::header('warn', 'CRAWLING HAS STARTED WITH BASEURL: ' . $domain);

        return $httpUri;
    }

    public static function baseUrl()
    {
        return self::$baseurl;
    }

}
