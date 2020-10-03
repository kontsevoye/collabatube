<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\AbstractController;
use App\Middleware\ApiAuthenticateMiddleware;
use Hyperf\HttpMessage\Exception\UnauthorizedHttpException;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\HttpServer\Contract\ResponseInterface;
use HyperfExt\Auth\Contracts\AuthenticatableInterface;
use HyperfExt\Auth\Contracts\AuthManagerInterface;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;

/**
 * @Controller(prefix="/api/v1")
 * @Middleware(ApiAuthenticateMiddleware::class)
 */
class ApiController extends AbstractController
{
    private AuthenticatableInterface $user;

    public function __construct(AuthManagerInterface $authManager)
    {
        $user = $authManager->guard()->user();
        if ($user === null) {
            throw new UnauthorizedHttpException();
        }

        $this->user = $user;
    }

    /**
     * @RequestMapping(path="hello", methods="get")
     */
    public function hello(ResponseInterface $response): PsrResponseInterface
    {
        return $response
            ->json(['userId' => $this->user->getAuthIdentifier()])
            ->withStatus(200);
    }
}
