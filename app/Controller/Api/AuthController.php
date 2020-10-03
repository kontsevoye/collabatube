<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\AbstractController;
use App\Model\User;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\HttpServer\Contract\ResponseInterface;
use HyperfExt\Jwt\Jwt;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;

/**
 * @Controller(prefix="/api/v1/auth")
 */
class AuthController extends AbstractController
{
    /**
     * @RequestMapping(path="mock", methods="post")
     */
    public function __invoke(ResponseInterface $response, Jwt $jwt): PsrResponseInterface
    {
        $user = User::find(1);

        return $response
            ->json(['jwt' => $jwt->fromUser($user)])
            ->withStatus(200);
    }
}
