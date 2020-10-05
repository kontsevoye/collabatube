<?php

declare(strict_types=1);

namespace App\OAuth\Provider;

use App\Exception\OAuth\ExchangeCodeExceptionInterface;
use App\OAuth\Token\AccessTokenInterface;
use GuzzleHttp\Client;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Guzzle\ClientFactory;

abstract class AbstractProvider implements ProviderInterface
{
    protected ConfigInterface $config;

    protected ClientFactory $clientFactory;

    protected Client $client;

    public function __construct(ConfigInterface $config, ClientFactory $clientFactory)
    {
        $this->config = $config;
        $this->clientFactory = $clientFactory;
        $this->client = $this->clientFactory->create();
    }

    /**
     * @return string provider name, eg github, google, etc
     */
    abstract public function getProviderName(): string;

    /**
     * @return string
     */
    public function getAccessTokenFieldName(): string
    {
        return 'access_token';
    }

    /**
     * {@inheritdoc}
     */
    public function buildInitializeUrl(string $serverHost, string $lastLocation = '/'): string
    {
        $query = http_build_query([
            'client_id' => $this->config->get("oauth.providers.{$this->getProviderName()}.client_id"),
            'redirect_uri' => "https://{$serverHost}/auth/{$this->getProviderName()}/finish?last-location={$lastLocation}",
            'scope' => $this->config->get("oauth.providers.{$this->getProviderName()}.scope"),
            'response_type' => $this->config->get("oauth.providers.{$this->getProviderName()}.response_type"),
        ]);

        return "{$this->config->get("oauth.providers.{$this->getProviderName()}.authorize_url")}?{$query}";
    }

    /**
     * {@inheritdoc}
     */
    public function exchangeCode(string $serverHost, string $code, string $lastLocation = '/'): AccessTokenInterface
    {
        $response = $this->client->post(
            $this->config->get("oauth.providers.{$this->getProviderName()}.access_token_url"),
            [
                'timeout' => $this->config->get('oauth.exchange_timeout'),
                'json' => [
                    'client_id' => $this->config->get("oauth.providers.{$this->getProviderName()}.client_id"),
                    'client_secret' => $this->config->get("oauth.providers.{$this->getProviderName()}.client_secret"),
                    'code' => $code,
                    'grant_type' => $this->config->get("oauth.providers.{$this->getProviderName()}.grant_type"),
                    'redirect_uri' => "https://{$serverHost}/auth/{$this->getProviderName()}/finish?last-location={$lastLocation}",
                ],
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
            ]
        );
        $responseBody = (string) $response->getBody()->getContents();
        $responseData = json_decode($responseBody, true);
        $token = array_key_exists($this->getAccessTokenFieldName(), $responseData)
            ? $responseData[$this->getAccessTokenFieldName()]
            : null;
        if ($token === null) {
            throw new ExchangeCodeExceptionInterface("{$this->getAccessTokenFieldName()} is null");
        }

        return $this->getAccessTokenInstance()->loadAccessToken($token);
    }

    /**
     * @return AccessTokenInterface
     */
    abstract protected function getAccessTokenInstance(): AccessTokenInterface;
}
