<?php
declare(strict_types=1);

use Psancho\Comic\Model\User\Profile;
use Psancho\Galeizon\Adapter\SlimAdapter\Middleware\AuthorizationHandler;
use Psancho\Galeizon\Model\Auth\Requirements;

$adminSchema = new AuthorizationHandler((new Requirements)->forScope('admin_schema'));
$authorizeAdmin = new AuthorizationHandler((new Requirements)->forUser(Profile::Admin));
$authorizeAny = new AuthorizationHandler((new Requirements)->forUser(Profile::Simple));

return [
    'adminSchema' => $adminSchema,
    'mandatoryAdmin' => $authorizeAdmin,
    'mandatoryUser' => $authorizeAny,
];
