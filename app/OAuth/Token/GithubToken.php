<?php

declare(strict_types=1);

namespace App\OAuth\Token;

use App\Exception\OAuth\ResourceOwnerFieldMissingException;
use GuzzleHttp\Client;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Guzzle\ClientFactory;

class GithubToken extends AbstractToken
{
    protected ConfigInterface $config;

    protected Client $client;

    public function __construct(ConfigInterface $config, ClientFactory $clientFactory)
    {
        $this->config = $config;
        $this->client = $clientFactory->create();
    }

    /**
     * {@inheritdoc}
     */
    public function getResourceOwner(): ResourceOwnerInterface
    {
        $githubUserResponse = $this->client->get(
            'https://api.github.com/user',
            [
                'timeout' => $this->config->get('oauth.resource_owner_fetch_timeout'),
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Authorization' => "Bearer {$this->accessToken}",
                ],
            ]
        );
        $githubUserResponseBody = (string) $githubUserResponse->getBody()->getContents();
        $githubUserResponseData = json_decode($githubUserResponseBody, true);
        $githubUserId = array_key_exists('id', $githubUserResponseData)
            ? (string) $githubUserResponseData['id']
            : null;
        if ($githubUserId === null) {
            throw new ResourceOwnerFieldMissingException('user id is null');
        }
        $githubUserEmail = array_key_exists('email', $githubUserResponseData)
            ? (string) $githubUserResponseData['email']
            : null;
        if ($githubUserEmail === null) {
            throw new ResourceOwnerFieldMissingException('user email is null');
        }
        $githubUserAvatar = array_key_exists('avatar_url', $githubUserResponseData)
            ? (string) $githubUserResponseData['avatar_url']
            : null;
        if ($githubUserAvatar === null) {
            throw new ResourceOwnerFieldMissingException('user avatar is null');
        }

        return new GenericResourceOwner($githubUserId, $githubUserEmail, $githubUserAvatar);
    }
}
