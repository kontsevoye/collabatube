<?php

use HyperfExt\Hashing\Driver\BcryptDriver;

return [
    'driver' => [
        'bcrypt' => [
            'class' => BcryptDriver::class,
            'options' => [],
        ],
    ],
];