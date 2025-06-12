<?php
declare(strict_types=1);

use Psancho\Galeizon\Adapter\SlimAdapter\Middleware\AuthorizationHandler;
use Psancho\Galeizon\Model\Auth\Requirements;

$adminSchema = new AuthorizationHandler((new Requirements)->forScope('admin_schema'));
$authorizeAny = new AuthorizationHandler;

return [
    'adminSchema' => $adminSchema,
    'mandatoryUser' => $authorizeAny,
];
