<?php

declare(strict_types=1);

namespace App\Controller\Auth;

use App\Controller\AbstractController;
use App\Exception\OAuth\ExchangeCodeExceptionInterface;
use App\Exception\OAuth\ResourceOwnerFieldMissingException;
use App\Model\SocialAccount;
use App\Model\User;
use App\OAuth\Provider\ProviderInterface;
use Hyperf\Database\Model\ModelNotFoundException;
use Hyperf\DbConnection\Db;
use Hyperf\HttpMessage\Cookie\SetCookie;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use HyperfExt\Jwt\Jwt;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;

abstract class AbstractAuthController extends AbstractController
{
    abstract public function getProvider(): ProviderInterface;

    abstract public function getSocialAccountType(): int;

    public function startFlow(RequestInterface $request, ResponseInterface $response): PsrResponseInterface
    {
        return $response
            ->raw('')
            ->withStatus(302)
            ->withHeader(
                'Location',
                $this->getProvider()->buildInitializeUrl(
                    $request->getUri()->getHost(),
                    $request->query('last-location', '/')
                )
            );
    }

    /**
     * @throws ExchangeCodeExceptionInterface
     * @throws ResourceOwnerFieldMissingException
     */
    public function finishFlow(RequestInterface $request, ResponseInterface $response, Jwt $jwt): PsrResponseInterface
    {
        $lastLocation = $request->query('last-location', '/');
        $idToken = $this->getProvider()->exchangeCode(
            $request->getUri()->getHost(),
            $request->query('code'),
            $lastLocation
        );
        $ro = $idToken->getResourceOwner();

        $sa = null;
        Db::transaction(function () use (&$sa, $ro) {
            try {
                /** @var SocialAccount $sa */
                $sa = SocialAccount::query()
                    ->where('type', $this->getSocialAccountType())
                    ->where('social_id', $ro->getId())
                    ->firstOrFail();
            } catch (ModelNotFoundException $e) {
                $u = new User();
                $u->setPassword('');
                $u->save();
                $sa = new SocialAccount();
                $sa->setType($this->getSocialAccountType())->setSocialId($ro->getId());
                $sa->user()->associate($u);
            }
            $sa->setEmail($ro->getEmail())->setAvatarUrl($ro->getAvatarUrl());
            $sa->save();
        });

        /** @var User $user */
        $user = $sa->user()->first();
        $sc = new SetCookie([
            'Name' => 'ctjwt',
            'Value' => $jwt->fromUser($user),
            'Domain' => ".{$request->getUri()->getHost()}",
            'Path' => '/',
            'Max-Age' => 60,
            'Expires' => null,
            'Secure' => true,
            'Discard' => false,
            'HttpOnly' => true,
        ]);

        return $response
            ->raw('')
            ->withStatus(302)
            ->withHeader('Set-Cookie', (string) $sc)
            ->withHeader('Location', $lastLocation);
    }
}
