<style>
    .s200 {
        color: gray;
    }
    .s301 {
        color: darkblue;
    }
    .s404 {
        color: crimson;
    }
    strong {
        color: green;
    }
</style>
<?php

use GuzzleHttp\Client;

include 'vendor/autoload.php';

$baseuri = 'http://www.adrianorosa.com';
$craw_data = "data/index/craw-www.adrianorosa.com.json";

$base301='www.301-migrated-location-url.com';

$client = new Client([
    'base_uri' => $baseuri,
    'allow_redirects' => false,
]);

$file = file_get_contents($craw_data);

$link = json_decode($file, true);
echo "<h3>$baseuri</h3>";
echo '<pre>';

foreach ($link as $key => $url) {

    if ( ! in_array(strtolower($url['extension']), ['js', 'css', 'img', 'png', 'gif', 'jpg', 'jpeg']) ) {
        continue;
    }

    try {

        $response = $client->head($url['rawUrl']);

        $content = (string) $response->getBody();
        $reason = $response->getReasonPhrase();

        $status = $response->getStatusCode();

        echo '<span class="s'.$status.'">';
        echo $status.' <small>'.$reason.'</small> ';
        echo str_pad(substr($url['rawUrl'],0, 60), 70, " ");
        if ( $status === 301 ) {
            $location = $response->getHeaderLine('location');
            if ( preg_match('#'.$base301.'#', $location) ) {
                echo '<strong>'. $response->getHeaderLine('location').'</strong>';
            } else {
                echo $response->getHeaderLine('location');
            }

        }
        echo '</span>';
        echo PHP_EOL;


    } catch (GuzzleHttp\Exception\ClientException $e) {

        $status = $e->getCode();
        $message = '';//substr($e->getMessage(),0, 90);
        $url = (string) $e->getRequest()->getUri();
        $reason = $e->getResponse()->getReasonPhrase();
        echo '<span class="s'.$status.'">';
        echo $status.' '.$reason.' ';
        echo $url.' ';
        echo $message;
        echo '</span>';
        echo PHP_EOL;
    }
}

//var_dump($link); exit;
