<?php namespace Engine;

use Engine\Crawler\Parser;
use Engine\Crawler\Resource;

/**
 * Class Crawler
 *
 * @author  Adriano Rosa <http://adrianorosa.com>
 * @date    18/03/16 12:29
 *
 * @package Engine
 */
class Crawler
{
    /**
     * @var \Engine\Crawler\Resource
     */
    protected $url;

    protected $errorMessage;

    protected static $pages = [];

    /**
     * @var static
     */
    protected static $instance;

    /**
     * Crawler constructor.
     *
     * @param \Engine\Crawler\Resource $url
     *
     */
    public function __construct(\Engine\Crawler\Resource $url)
    {
        $this->url = $url;
        $this->register($this);
    }

    public static function create($domain)
    {
        $baseurl = Domain::setBaseUrl($domain);

        $resource = new Resource($baseurl);

        return new static($resource);
    }

    public static function setInstance($container)
    {
        static::$instance = $container;
    }

    protected function register($container)
    {
        static::$instance = $container;
    }

    public static function getInstance()
    {
        return self::$instance;
    }

    public function craw()
    {
        Console::success(sprintf(
            'Crawling URL %s ...',
            $this->url
        ));

        $client = HttpClient::getClient();

        $indexUrl = new Parser();
        $resource = $this->url;
        //var_dump($resource); exit;

        $content = '';
        // ------------------------------------------------------
        // GET THE CONTENT OF EACH URL
        // ------------------------------------------------------
        try {

            $response = $client->request('GET', $resource->getRawUrl());

            if ( ($status = $response->getStatusCode()) === 200 ) {

                $content = (string) $response->getBody();

                $this->storageContent($resource->getPath(), $resource->getBasename(), $content);

                $indexUrl->updateIndex($content);

            } elseif ($status === 301) {

                $location = implode('', $response->getHeader('location'));

                Console::setOutput('info', sprintf(
                    'Response Status: %s: from %s to: %s',
                    $status, $resource, $location
                ));

            } else {

                $this->errorMessage =  sprintf('Response Status Error: $s %s', $status, $resource);
            }

        } catch (\Exception $e) {

            $this->errorMessage = $e->getMessage();
        }

        if ( empty($content) ) {
            if ( $this->errorMessage ) {
                Console::setOutput('error', $this->errorMessage);
            }
        }
    }

    protected function storageContent($dirname, $filename, $content)
    {
        if ( empty($content) ) {
            return;
        }

        $dirname = trim(Config::get('storage-path', 'html'),'/').DIRECTORY_SEPARATOR.trim($dirname, '/');
        $filename = trim($dirname, '/').DIRECTORY_SEPARATOR.trim($filename, '/');

        // Create a nested directory for the given path
        Dir::make($dirname);

        // --------------------------------------------
        // Conversion Content Encoding
        // --------------------------------------------
        if ( preg_match('/\.(html|css|js)$/i', $filename) ) {
            $content = Encoding::toUTF8($content);
            $content = NormalizeContent::cleanAuthority($content);
        }

        $create = @file_put_contents($filename, $content);

        if ( $create === false ) {
            Console::setOutput('error', 'Permission Error on save: ' . $filename);
            exit(1);
        }

        //Console::setOutput('success', 'Content has been created: ' . $filename);
    }
}
