<?php

declare(strict_types=1);

return [
    \Hyperf\SocketIOServer\Room\AdapterInterface::class => \Hyperf\SocketIOServer\Room\RedisAdapter::class,
];
