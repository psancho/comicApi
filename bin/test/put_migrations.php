#!/usr/bin/env php
<?php

declare(strict_types=1);

use GuzzleHttp\RequestOptions;
use Psancho\Galeizon\Adapter\MigrationsAdapter;
use Psancho\Galeizon\Adapter\SelfAdapter;
use Psancho\Galeizon\App;
use Psancho\Galeizon\Model\Auth\OwnerType;

require dirname(__DIR__, 2) . '/vendor/psancho/galeizon/src/App.php';

$go = microtime(true);

try {
    $app = App::getInstance();

    $expectedLatest = MigrationsAdapter::getExpectedLatest();
    if ($expectedLatest === '') {
        return;
    }

    $response = SelfAdapter::getInstance()->sendRequest(

        'PUT',
        'migrations',
        [
            RequestOptions::HEADERS => ['accept' => 'application/json'],
            RequestOptions::BODY => $expectedLatest,
            RequestOptions::DEBUG => true,
            RequestOptions::QUERY => [
                'nocache' => 1,
            ],
        ],
        OwnerType::client,
        'admin_schema',
    );

} catch (Throwable $e) {
    echo $e;

} finally {
    $duration = microtime(true) - $go;
    printf("\n...terminé après %.3f s\n", $duration);
}
