<?php
declare(strict_types=1);

use Psancho\Galeizon\Adapter\LogAdapter;
use Psancho\Galeizon\Adapter\SlimAdapter;
use Psancho\Galeizon\App;
use Psancho\Galeizon\Model\FireAndForget;

require dirname(__DIR__) . '/vendor/psancho/galeizon/src/App.php';

try {
    App::getInstance();
    SlimAdapter::getInstance()->listen();

    if (FireAndForget::getInstance()->hasJobs()) {
        FireAndForget::getInstance()->run();
    }

} catch (Throwable $e) {
    LogAdapter::error($e);
    if (!FireAndForget::getInstance()->hasJobs()) {
        http_response_code(500);
    }
}
