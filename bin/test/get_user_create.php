#!/usr/bin/env php
<?php

declare(strict_types=1);

use GuzzleHttp\RequestOptions;
use Psancho\Galeizon\Adapter\MigrationsAdapter;
use Psancho\Galeizon\Adapter\SelfAdapter;
use Psancho\Galeizon\App;
use Psancho\Galeizon\Model\Auth\AuthorizationRegistration;
use Psancho\Galeizon\Model\Auth\OwnerType;
use Psancho\Galeizon\Model\Auth\Registration;
use Psancho\Galeizon\Model\Conf;
use Psancho\Galeizon\View\Json;

require dirname(__DIR__, 2) . '/vendor/psancho/galeizon/src/App.php';

$go = microtime(true);

try {
    $app = App::getInstance();

    $registration = Registration::fromObject((object) [
        "username" => $app->conf->self->username,
        "password" => $app->conf->self->password,
        "email" => "psancho.13@gmail.com",
        "firstname" => "Pascal",
        "lastname" => "Sancho",
    ]);
    $token = AuthorizationRegistration::genTokenRegistration($registration);
    $payload = Json::serialize($registration);

    $response = SelfAdapter::getInstance()->sendRequest(
        'GET',
        'authc/users/create',
        [
            RequestOptions::HEADERS => ['accept' => 'application/json'],
            RequestOptions::BODY => $payload,
            RequestOptions::DEBUG => true,
            RequestOptions::QUERY => [
                "token" => $token,
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
