<?php namespace Engine\Crawler;

use Engine\Config;
use Engine\Console;
use Engine\Crawler;
use Engine\Domain;
use League\Uri\Schemes\Http as HttpUri;

class Parser
{
    protected static $index = [];

    protected $baseHost;

    protected $content;

    /**
     * ImageIndex constructor.
     *
     * @param null $baseHost
     */
    public function __construct($baseHost = null)
    {
        $this->baseHost = ($baseHost instanceof HttpUri) ? $baseHost : HttpUri::createFromString(Domain::baseUrl());

        //$this->baseHost = $baseHost ?: Domain::baseUrl();
    }

    /**
     * @param string $content
     *
     * @return $this
     */
    public function updateIndex($content)
    {
        if ( ! empty($content) ) {

            $html = $this->content = $content;

            $document = new \DOMDocument();
            @$document->loadHTML($html);

            $this->parseHTML($document);
            $this->parseCSS($document);
            $this->parseJS($document);
            $this->parseIMG($document);
        }

        return $this;
    }

    public function addIndex(HttpUri $uri)
    {
        $resource = new Resource($uri);
        $key = $resource->getHash();

        if ( ! isset(self::$index[$key]) ) {

            self::$index[$key] = [
                'basename' => $resource->getBasename(),
                'extension' => $resource->getExtension(),
                'path' => $resource->getPath(),
                'rawUrl' => $resource->getRawUrl(),
                //'hash' => $resource->getHash(),
                //'is_crawled' => false,
                //'contentHash' => sha1($this->content),
            ];

            $craw = new Crawler($resource);
            $craw->craw();

        } else {

            //Console::info('URL Already Crawled: ' . $resource->getRawUrl());
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function buildIndex()
    {
        // remove duplicates
        $list = array_unique(self::$index, SORT_REGULAR);
        $data = $list;

        $result = file_put_contents(
            // FIXME domain gets empty when there is no http:// in the config
            'data/index/craw-'.parse_url(Config::get('domain'), PHP_URL_HOST).'.json',
            json_encode($data, JSON_FORCE_OBJECT | JSON_UNESCAPED_SLASHES)
        );

        if ( false !== $result ) {
            Console::header('text', sprintf('URL Index was build successfully'));
            return true;
        }

        Console::setOutput('error', 'data/index/craw.json build error.');
        exit(1);
    }

    protected function parseHTML(\DOMDocument $document)
    {
        $link = $document->getElementsByTagName('a');

        foreach ($link as $tag) {

            /**@var \DOMElement $tag*/
            $href = $tag->getAttribute('href');

            if ( preg_match('/^mailto|#|javascript/i', $href, $matches)  ) {
                Console::alert(sprintf("Type of url {$matches[0]} was not parsed: %s", $href));
                continue;
            }

            $url = HttpUri::createFromString($href);

            if ( $this->isExternalHost($url, 'HTML') ) {
                continue;
            }

            $this->addIndex($url);
        }

        return $this;
    }

    protected function parseCSS(\DOMDocument $document)
    {
        $css = $document->getElementsByTagName('link');

        foreach ($css as $tag) {

            /**@var \DOMElement $tag*/
            $src = $tag->getAttribute('href');
            $type = $tag->getAttribute('type');

            if ( !in_array($type, ['text/css', 'stylesheet']) ) {
                continue;
            }

            $url = HttpUri::createFromString($src);

            if ( $this->isExternalHost($url, 'CSS') ) {
                continue;
            }

            if ( preg_match('/css/i', strtolower($url->path->getExtension())) ) {

                $this->addIndex($url);
            }
        }

        return $this;
    }

    protected function parseJS(\DOMDocument $document)
    {
        $js = $document->getElementsByTagName('script');

        foreach ($js as $tag) {

            /**@var \DOMElement $tag*/
            $src = $tag->getAttribute('src');

            $url = HttpUri::createFromString($src);

            if ( $this->isExternalHost($url, 'JS') ) {
                continue;
            }

            if ( preg_match('/js/i', strtolower($url->path->getExtension())) ) {

                $this->addIndex($url);
            }
        }

        return $this;
    }

    protected function parseIMG(\DOMDocument $document)
    {
        $images = $document->getElementsByTagName('img');

        foreach ($images as $tag) {

            /**@var \DOMElement $tag */
            $src = $tag->getAttribute('src');

            $url = HttpUri::createFromString($src);

            if ( $this->isExternalHost($url, 'IMG') ) {
                continue;
            }

            if (preg_match('/(jpe?g|png|gif)$/i', strtolower($url->path->getExtension()))) {

                $this->addIndex($url);
            }
        }
    }

    protected function isExternalHost(HttpUri $url, $typeCheck)
    {
        $host = $url->getHost();
        $typeCheck = strtoupper($typeCheck);

        if ( !empty($host) && $host !== $this->baseHost->getHost() ) {
            Console::setOutput('text', sprintf("External {$typeCheck} Resource: %s", $url));
            return true;
        }

        return false;
    }
}
