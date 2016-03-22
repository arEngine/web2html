<?php
namespace Engine;

use Engine\Config;
use Engine\Console;
use Engine\Crawler;
/**
 * Class App
 *
 * @author  Adriano Rosa <http://adrianorosa.com>
 * @date    19/03/16 17:44
 *
 * @package Engine
 */
class App
{
    protected $options = [];

    protected $defaults = [
        'domain' => null,
        'storage-path' => 'html',
        'follow-redirects' => false,
        'config' => null,
    ];

    protected $config = [];

    /**
     * App constructor.
     */
    public function __construct()
    {
        $this->bootstrap();
    }

    /**
     * @return array
     */
    public function bootstrap()
    {
        $args = [];

        if (defined("STDIN")) {

            $longopts = [
                'domain:',
                'storage-path:',
                'follow-redirects:',
                'config:',
            ];

            $args = getopt('', $longopts);

            if (!empty($args['follow-redirects'])) {
                $args['follow-redirects'] = filter_var($args['follow-redirects'], FILTER_VALIDATE_BOOLEAN);
            }

        } else {

            if (!empty($_GET['domain'])) {
                $args['domain'] = filter_var($_GET['domain'], FILTER_SANITIZE_STRING);
            }

            if (!empty($_GET['storage-path'])) {
                $args['storage-path'] = filter_var($_GET['storage-path'], FILTER_SANITIZE_STRING);
            }

            if (!empty($_GET['follow-redirects'])) {
                $args['follow-redirects'] = filter_var($_GET['follow-redirects'], FILTER_VALIDATE_BOOLEAN);
            }

            if (!empty($_GET['config'])) {
                $args['config'] = filter_var($_GET['config'], FILTER_SANITIZE_STRING);
            }
        }

        $config = Config::boot(isset($args['config']) ? $args['config'] : null);

        $options = array_merge($this->defaults, $config, $args);

        if (empty($options['domain'])) {
            Console::text("Domain Required, usage:");
            Console::alert("php migrate.php --domain=www.example.com --storage-path=[path] --follow-redirects=[bool]");
            exit(1);
        }

        Config::set($options);
    }

    public function run()
    {
        $craw = Crawler::create(Config::get('domain'));
        $craw->craw();

        $index = new Crawler\Parser();
        $index->buildIndex();

        Console::header('info', sprintf('Time Taken: %s seconds', time_taken()));
    }
}
