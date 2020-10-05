<?php

declare(strict_types=1);

namespace App\Exception\OAuth;

use App\Exception\AppExceptionInterface;

class ResourceOwnerFieldMissingException extends \Exception implements AppExceptionInterface
{
}
