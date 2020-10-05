<?php

declare(strict_types=1);

namespace App\Controller\Auth;

use App\Model\SocialAccount;
use App\OAuth\Provider\GoogleProvider;
use App\OAuth\Provider\ProviderInterface;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use HyperfExt\Jwt\Jwt;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;

/**
 * @Controller(prefix="/auth/google")
 */
class GoogleAuthController extends AbstractAuthController
{
    private GoogleProvider $provider;

    public function __construct(GoogleProvider $provider)
    {
        $this->provider = $provider;
    }

    /**
     * {@inheritdoc}
     */
    public function getProvider(): ProviderInterface
    {
        return $this->provider;
    }

    /**
     * {@inheritdoc}
     */
    public function getSocialAccountType(): int
    {
        return SocialAccount::TYPE_GOOGLE;
    }

    /**
     * @RequestMapping(path="start", methods="get")
     * {@inheritdoc}
     */
    public function startFlow(RequestInterface $request, ResponseInterface $response): PsrResponseInterface
    {
        return parent::startFlow($request, $response);
    }

    /**
     * @RequestMapping(path="finish", methods="get")
     * {@inheritdoc}
     */
    public function finishFlow(RequestInterface $request, ResponseInterface $response, Jwt $jwt): PsrResponseInterface
    {
        return parent::finishFlow($request, $response, $jwt);
    }
}
