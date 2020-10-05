<?php

declare(strict_types=1);

namespace App\Middleware;

use Hyperf\HttpServer\Contract\ResponseInterface;
use HyperfExt\Auth\Contracts\AuthManagerInterface;
use HyperfExt\Auth\Exceptions\AuthenticationException;
use HyperfExt\Auth\Middlewares\AbstractAuthenticateMiddleware;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ApiAuthenticateMiddleware extends AbstractAuthenticateMiddleware
{
    private ResponseInterface $response;

    public function __construct(AuthManagerInterface $auth, ResponseInterface $response)
    {
        $this->response = $response;
        parent::__construct($auth);
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): PsrResponseInterface
    {
        try {
            return parent::process($request, $handler);
        } catch (AuthenticationException $e) {
            return $this->response->json(['message' => $e->getMessage()])->withStatus(401);
        }
    }

    /**
     * Get guard names.
     *
     * @return string[]
     */
    protected function guards(): array
    {
        return [
            'api',
        ];
    }
}
