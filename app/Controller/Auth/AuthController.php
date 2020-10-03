<?php

declare(strict_types=1);

namespace App\Controller\Auth;

use App\Controller\AbstractController;
use App\Model\SocialAccount;
use App\Model\User;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Database\Model\ModelNotFoundException;
use Hyperf\DbConnection\Db;
use Hyperf\Guzzle\ClientFactory;
use Hyperf\HttpMessage\Cookie\SetCookie;
use Hyperf\HttpMessage\Exception\UnauthorizedHttpException;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use HyperfExt\Jwt\Jwt;
use Lcobucci\JWT\Parser;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;

/**
 * @Controller(prefix="/auth")
 */
class AuthController extends AbstractController
{
    /**
     * @RequestMapping(path="github/start", methods="get")
     */
    public function startGithubFlow(
        RequestInterface $request,
        ResponseInterface $response,
        ConfigInterface $config
    ): PsrResponseInterface {
        $lastLocation = $request->query('last-location', '/');
        $uri = $request->getUri();
        $query = http_build_query([
            'client_id' => $config->get('oauth.providers.github.client_id'),
            'redirect_uri' => "https://{$uri->getHost()}/auth/github/finish?last-location={$lastLocation}",
            'scope' => $config->get('oauth.providers.github.scope'),
            'response_type' => $config->get('oauth.providers.google.response_type'),
        ]);

        return $response
            ->raw('')
            ->withStatus(302)
            ->withHeader('Location', "{$config->get('oauth.providers.github.authorize_url')}?{$query}");
    }

    /**
     * @RequestMapping(path="github/finish", methods="get")
     */
    public function finishGithubFlow(
        RequestInterface $request,
        ResponseInterface $response,
        ConfigInterface $config,
        ClientFactory $clientFactory,
        Jwt $jwt
    ): PsrResponseInterface {
        $lastLocation = $request->query('last-location', '/');
        $code = $request->query('code');
        if ($code === null) {
            throw new UnauthorizedHttpException('code is null');
        }

        $client = $clientFactory->create();
        $githubResponse = $client->post(
            $config->get('oauth.providers.github.access_token_url'),
            [
                'timeout' => 10,
                'json' => [
                    'client_id' => $config->get('oauth.providers.github.client_id'),
                    'client_secret' => $config->get('oauth.providers.github.client_secret'),
                    'code' => $code,
                    'grant_type' => $config->get('oauth.providers.github.grant_type'),
                    'redirect_uri' => "https://{$request->getUri()->getHost()}/auth/github/finish?last-location={$lastLocation}",
                ],
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
            ]
        );
        $githubResponseBody = (string) $githubResponse->getBody()->getContents();
        $githubResponseData = json_decode($githubResponseBody, true);
        $accessToken = array_key_exists('access_token', $githubResponseData)
            ? $githubResponseData['access_token']
            : null;
        if ($accessToken === null) {
            throw new UnauthorizedHttpException('access token is null');
        }

        $githubUserResponse = $client->get(
            'https://api.github.com/user',
            [
                'timeout' => 10,
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Authorization' => "Bearer {$accessToken}",
                ],
            ]
        );
        $githubUserResponseBody = (string) $githubUserResponse->getBody()->getContents();
        $githubUserResponseData = json_decode($githubUserResponseBody, true);
        $githubUserId = array_key_exists('id', $githubUserResponseData)
            ? (string) $githubUserResponseData['id']
            : null;
        if ($githubUserId === null) {
            throw new UnauthorizedHttpException('user id is null');
        }
        $githubUserEmail = array_key_exists('email', $githubUserResponseData)
            ? (string) $githubUserResponseData['email']
            : null;
        if ($githubUserEmail === null) {
            throw new UnauthorizedHttpException('user email is null');
        }
        $githubUserAvatar = array_key_exists('avatar_url', $githubUserResponseData)
            ? (string) $githubUserResponseData['avatar_url']
            : null;
        if ($githubUserAvatar === null) {
            throw new UnauthorizedHttpException('user avatar is null');
        }

        $sa = null;
        Db::transaction(function () use (&$sa, $githubUserId, $githubUserEmail, $githubUserAvatar) {
            try {
                $sa = SocialAccount::query()
                    ->where('type', SocialAccount::TYPE_GITHUB)
                    ->where('social_id', $githubUserId)
                    ->firstOrFail();
            } catch (ModelNotFoundException $e) {
                $u = new User();
                $u->setPassword('');
                $u->save();
                $sa = new SocialAccount();
                $sa->setType(SocialAccount::TYPE_GITHUB)->setSocialId($githubUserId);
                $sa->user()->associate($u);
            }
            $sa->setEmail($githubUserEmail)->setAvatarUrl($githubUserAvatar);
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

    /**
     * @RequestMapping(path="google/start", methods="get")
     */
    public function startGoogleFlow(
        RequestInterface $request,
        ResponseInterface $response,
        ConfigInterface $config
    ): PsrResponseInterface {
        $lastLocation = $request->query('last-location', '/');
        $uri = $request->getUri();
        $query = http_build_query([
            'client_id' => $config->get('oauth.providers.google.client_id'),
            'redirect_uri' => "https://{$uri->getHost()}/auth/google/finish?last-location={$lastLocation}",
            'scope' => $config->get('oauth.providers.google.scope'),
            'response_type' => $config->get('oauth.providers.google.response_type'),
        ]);

        return $response
            ->raw('')
            ->withStatus(302)
            ->withHeader('Location', "{$config->get('oauth.providers.google.authorize_url')}?{$query}");
    }

    /**
     * @RequestMapping(path="google/finish", methods="get")
     */
    public function finishGoogleFlow(
        RequestInterface $request,
        ResponseInterface $response,
        ConfigInterface $config,
        ClientFactory $clientFactory,
        Jwt $jwt
    ): PsrResponseInterface {
        $lastLocation = $request->query('last-location', '/');
        $code = $request->query('code');
        if ($code === null) {
            throw new UnauthorizedHttpException('code is null');
        }

        $client = $clientFactory->create();
        $oauthResponse = $client->post(
            $config->get('oauth.providers.google.access_token_url'),
            [
                'timeout' => 10,
                'json' => [
                    'client_id' => $config->get('oauth.providers.google.client_id'),
                    'client_secret' => $config->get('oauth.providers.google.client_secret'),
                    'code' => $code,
                    'grant_type' => $config->get('oauth.providers.google.grant_type'),
                    'redirect_uri' => "https://{$request->getUri()->getHost()}/auth/google/finish?last-location={$lastLocation}",
                ],
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
            ]
        );
        $oauthResponseBody = (string) $oauthResponse->getBody()->getContents();
        $oauthResponseData = json_decode($oauthResponseBody, true);
        $idToken = array_key_exists('id_token', $oauthResponseData)
            ? $oauthResponseData['id_token']
            : null;
        if ($idToken === null) {
            throw new UnauthorizedHttpException('id token is null');
        }

        $token = (new Parser())->parse($idToken);
        $oauthUserId = $token->getClaim('sub');
        if ($oauthUserId === null) {
            throw new UnauthorizedHttpException('user id is null');
        }
        $oauthUserEmail = $token->getClaim('email');
        if ($oauthUserEmail === null) {
            throw new UnauthorizedHttpException('user email is null');
        }
        $oauthUserAvatar = $token->getClaim('picture');
        if ($oauthUserAvatar === null) {
            throw new UnauthorizedHttpException('user picture is null');
        }

        $sa = null;
        Db::transaction(function () use (&$sa, $oauthUserId, $oauthUserEmail, $oauthUserAvatar) {
            try {
                $sa = SocialAccount::query()
                    ->where('type', SocialAccount::TYPE_GOOGLE)
                    ->where('social_id', $oauthUserId)
                    ->firstOrFail();
            } catch (ModelNotFoundException $e) {
                $u = new User();
                $u->setPassword('');
                $u->save();
                $sa = new SocialAccount();
                $sa->setType(SocialAccount::TYPE_GOOGLE)->setSocialId($oauthUserId);
                $sa->user()->associate($u);
            }
            $sa->setEmail($oauthUserEmail)->setAvatarUrl($oauthUserAvatar);
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
