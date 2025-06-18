<?php
declare(strict_types = 1);

namespace Psancho\Comic\Control;

use Psancho\Comic\Model\Book;
use Psancho\Comic\Model\Book\Filter;
use Psancho\Comic\Model\L10n;
use Psancho\Galeizon\Adapter\SlimAdapter\Endpoint;
use Psancho\Galeizon\Control\SlimController;
use Psancho\Galeizon\Model\Database\Paging;
use Psancho\Galeizon\Model\Locale;
use Psancho\Galeizon\View\Json;
use Psancho\Galeizon\View\StatusCode;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\ServerRequest;

class BooksController extends SlimController
{
    #[Endpoint(verb: "GET", path: "/books", authz: 'mandatoryUser')]
    public function get(ServerRequest $request, ResponseInterface $response): ResponseInterface
    {
        if (!self::isAcceptedJson($request)) {
            return $response->withStatus(StatusCode::HTTP_406_NOT_ACCEPTABLE);
        }

        $bookFilter = new Filter(
            paging: new Paging(
                self::getParamAsInt($request, 'perPage') ?? 10,
                self::getParamAsInt($request, 'page') ?? 1
            ),
            q: self::getParamAsString($request, 'q'),
            series: self::getParamAsString($request, 'series'),
            title: self::getParamAsString($request, 'title'),
            publisher: self::getParamAsString($request, 'publisher'),
            author: self::getParamAsString($request, 'author'),
            contribution: self::getParamAsString($request, 'contribution'),
            owned: self::getParamAsBool($request, 'owned'),
            authorSeries: self::getParamAsBool($request, 'authorSeries'),
            biopic: self::getParamAsBool($request, 'biopic'),
        );
        $bookFilter->parseSort((string) self::getParamAsString($request, 'sort'));

        $countItem = Book::count($bookFilter);

        assert(!is_null($bookFilter->paging));
        $linkHeaderValue = self::genLinkHeader($countItem, $bookFilter->paging);

        $books = Book::list($bookFilter);
        $json = Json::serialize($books);

        $response->getBody()->write($json);
        return $response
            ->withHeader('Access-Control-Expose-Headers', 'Link,X-Total-Count')
            ->withHeader('Link', $linkHeaderValue)
            ->withHeader('X-Total-Count', (string) $countItem)
            ;
    }
}
