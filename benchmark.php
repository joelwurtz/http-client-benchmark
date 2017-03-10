<?php

$directory = __DIR__ . DIRECTORY_SEPARATOR . $argv[1];

require_once $directory . '/vendor/autoload.php';

class MissingStrategies implements \Http\Discovery\Strategy\DiscoveryStrategy {
    public static function getCandidates($type)
    {
        return [
            ['class' => \Http\Adapter\Zend\Client::class, 'condition' => \Http\Adapter\Zend\Client::class],
            ['class' => \Http\Adapter\Cake\Client::class, 'condition' => \Http\Adapter\Cake\Client::class]
        ];
    }
};

\Http\Discovery\HttpClientDiscovery::appendStrategy(MissingStrategies::class);

$client = \Http\Discovery\HttpClientDiscovery::find();
$pluginClient = new \Http\Client\Common\PluginClient($client, [
    new \Http\Client\Common\Plugin\ContentLengthPlugin(),
    new \Http\Client\Common\Plugin\DecoderPlugin()
]);

$httpClient = new \Http\Client\Common\HttpMethodsClient($pluginClient, \Http\Discovery\MessageFactoryDiscovery::find());

$duration = 10;
$start = time();
$request = 0;

while (true) {
    $httpClient->get('http://127.0.0.1:8081');
    $request++;

    if (($request % 100) === 0) {
        if ((time() - $start) > $duration) {
            break;
        }
    }
}

$totalTime = time() - $start;

echo "Request counts : " . $request . "\n";
echo "Average time par request : " . ($totalTime / $request) * 1000 . "ms\n";
echo "Request per second : " . $request / $totalTime . "\n";
echo "Total time " . $totalTime . "\n";

