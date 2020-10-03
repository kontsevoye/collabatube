<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\AbstractController;
use App\Middleware\ApiAuthenticateMiddleware;
use App\Model\User;
use Hyperf\HttpMessage\Exception\UnauthorizedHttpException;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\HttpServer\Contract\ResponseInterface;
use HyperfExt\Auth\Contracts\AuthManagerInterface;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;

/**
 * @Controller(prefix="/api/v1/user")
 * @Middleware(ApiAuthenticateMiddleware::class)
 */
class UserController extends AbstractController
{
    private User $user;

    public function __construct(AuthManagerInterface $authManager)
    {
        $user = $authManager->guard()->user();
        if (!($user instanceof User)) {
            throw new UnauthorizedHttpException();
        }

        $this->user = $user;
    }

    /**
     * @RequestMapping(path="me", methods="get")
     */
    public function hello(ResponseInterface $response): PsrResponseInterface
    {
        return $response
            ->json(['user' => $this->user->toArray()])
            ->withStatus(200);
    }
}
