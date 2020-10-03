<?php

declare(strict_types=1);

namespace App\Controller;

use Hyperf\HttpMessage\Cookie\SetCookie;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;

/**
 * @Controller
 */
class IndexController extends AbstractController
{
    /**
     * @RequestMapping(path="/", methods="get")
     */
    public function index(RequestInterface $request, ResponseInterface $response): PsrResponseInterface
    {
        $jwt = $request->cookie('ctjwt', null);
        $sc = new SetCookie([
            'Name' => 'ctjwt',
            'Value' => 'DELETED',
            'Domain' => ".{$request->getUri()->getHost()}",
            'Path' => '/',
            'Max-Age' => 0,
            'Expires' => null,
            'Secure' => true,
            'Discard' => true,
            'HttpOnly' => true,
        ]);

        return $response
            ->json(['jwt' => $jwt])
            ->withHeader('Set-Cookie', (string) $sc);
    }
}
