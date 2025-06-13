#!/usr/bin/env php
<?php
declare(strict_types=1);

use GuzzleHttp\RequestOptions;
use Psancho\Galeizon\Adapter\SelfAdapter;
use Psancho\Galeizon\App;

require dirname(__DIR__, 2) . '/vendor/psancho/galeizon/src/App.php';

$go = microtime(true);

try {
    $app = App::getInstance();

    $response = SelfAdapter::getInstance()->sendRequest(

        'GET',
        'l10n/webapp/fr-FR',
        [
            RequestOptions::HEADERS => ['accept' => 'application/json'],
            RequestOptions::DEBUG => true,
            RequestOptions::QUERY => [
                'nocache' => 1,
            ],
        ],
    );
    SelfAdapter::printBody($response->getBody());

} catch (Throwable $e) {
    echo $e;

} finally {
    $duration = microtime(true) - $go;
    printf("\n...terminé après %.3f s\n", $duration);
}
