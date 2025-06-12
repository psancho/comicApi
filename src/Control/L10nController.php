<?php
declare(strict_types = 1);

namespace Psancho\Comic\Control;

use Psancho\Comic\Model\L10n;
use Psancho\Galeizon\Adapter\SlimAdapter\Endpoint;
use Psancho\Galeizon\Control\SlimController;
use Psancho\Galeizon\Model\Locale;
use Psancho\Galeizon\View\Json;
use Psancho\Galeizon\View\StatusCode;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\ServerRequest;

class L10nController extends SlimController
{
    /** @param array{ scope: string, locale?: string } $args */
    #[Endpoint(verb: "GET", path: "/l10n/{scope}[/{locale:\w{2}(?:-[^/]+)*}]")]
    public function get(ServerRequest $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (!self::isAcceptedJson($request)) {
            return $response->withStatus(StatusCode::HTTP_406_NOT_ACCEPTABLE);
        }

        $scope = '';
        $_locale = '';
        extract($args, EXTR_IF_EXISTS);

        $acceptedLocales = Locale::localesFromRequest($request);
        // BUG phpstan qui n'aime pas $locale
        if ($_locale !== '') {// @phpstan-ignore notIdentical.alwaysFalse
            array_unshift($acceptedLocales, $_locale);
        }

        $l10n = new L10n($acceptedLocales, $scope);
        $labels = $l10n->export();

        $json = Json::serialize($labels);
        $headerLink = "/l10n/$scope/" . $l10n->getSelectedLocale();

        $response->getBody()->write($json);
        return $response
            ->withHeader('Access-Control-Expose-Headers', 'Link')
            ->withHeader('Content-Type', 'application/json')
            ->withHeader('Link', $headerLink);
    }
}
