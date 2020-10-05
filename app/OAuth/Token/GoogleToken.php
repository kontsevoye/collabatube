<?php

declare(strict_types=1);

namespace App\OAuth\Token;

use App\Exception\OAuth\ResourceOwnerFieldMissingException;
use Lcobucci\JWT\Parser;

class GoogleToken extends AbstractToken
{
    /**
     * {@inheritdoc}
     */
    public function getResourceOwner(): ResourceOwnerInterface
    {
        $token = (new Parser())->parse($this->accessToken);
        $oauthUserId = $token->getClaim('sub');
        if ($oauthUserId === null) {
            throw new ResourceOwnerFieldMissingException('user id is null');
        }
        $oauthUserEmail = $token->getClaim('email');
        if ($oauthUserEmail === null) {
            throw new ResourceOwnerFieldMissingException('user email is null');
        }
        $oauthUserAvatar = $token->getClaim('picture');
        if ($oauthUserAvatar === null) {
            throw new ResourceOwnerFieldMissingException('user picture is null');
        }

        return new GenericResourceOwner($oauthUserId, $oauthUserEmail, $oauthUserAvatar);
    }
}
