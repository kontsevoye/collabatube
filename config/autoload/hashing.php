<?php

declare(strict_types=1);

use HyperfExt\Hashing\Driver\BcryptDriver;

return [
    'driver' => [
        'bcrypt' => [
            'class' => BcryptDriver::class,
            'options' => [],
        ],
    ],
];
