<?php
namespace Engine\Crawler;

use League\Uri\Schemes\Http;

/**
 * Class Resource
 *
 * @author  Adriano Rosa <http://adrianorosa.com>
 * @date    18/03/16 16:28
 *
 * @package Engine
 */
class Resource
{
    protected $basename;

    protected $extension;

    protected $path;

    protected $rawUrl;

    protected $url;

    protected $hash;

    protected $httpUri;

    /**
     * Resource constructor.
     *
     * @param \League\Uri\Schemes\Http $httpUri
     */
    public function __construct(Http $httpUri)
    {
        $this->httpUri = $httpUri;
        $this->initialize();
    }

    public function initialize()
    {
        $this->rawUrl = (string) $this->httpUri;
        $basename = $this->httpUri->path->getBasename();
        $extension = strtolower($this->httpUri->path->getExtension());
        $path = empty($extension)
            ? (string) $this->httpUri->path->withoutTrailingSlash()
            : $this->httpUri->path->getDirname();

        if ( empty($basename) || ! preg_match('/\.(html|css|js|img|png|gif|jpe?g)$/i', $basename) ) {
            $basename = 'index.html';
            $extension = 'html';
        }

        $this->basename = $basename;
        $this->extension = $extension;
        $this->path = $path;
        $this->url = ((!empty($path)) ? '/' : '') . trim($path, '/') . '/' . $basename;

        $this->hash = sha1($this->rawUrl);

        unset($this->httpUri);
    }

    /**
     * @return string
     */
    public function getBasename()
    {
        return $this->basename;
    }

    /**
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return mixed
     */
    public function getRawUrl()
    {
        return $this->rawUrl;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return mixed
     */
    public function getHash()
    {
        return $this->hash;
    }


    public function __toString()
    {
        return $this->url;
    }

}
