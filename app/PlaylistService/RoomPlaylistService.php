<?php

declare(strict_types=1);

namespace App\PlaylistService;

use App\Model\Room;
use App\Serializer\JsonSerializer;
use Hyperf\Redis\Redis;

class RoomPlaylistService
{
    private Redis $redis;

    private JsonSerializer $serializer;

    public function __construct(Redis $redis, JsonSerializer $serializer)
    {
        $this->redis = $redis;
        $this->serializer = $serializer;
    }

    /**
     * @return PlaylistItem[]
     */
    public function list(Room $room): array
    {
        $key = $this->getRoomKey($room);
        $len = $this->redis->lLen($key);

        return array_map(
            fn (string $item) => $this->serializer->deserialize($item, PlaylistItem::class),
            $this->redis->lRange($key, 0, $len)
        );
    }

    /**
     * @return int the new length of the playlist
     */
    public function add(Room $room, PlaylistItem $item): int
    {
        return $this->redis->rPush($this->getRoomKey($room), $this->serializer->serialize($item));
    }

    private function getRoomKey(Room $room): string
    {
        return "room:{$room->getId()}";
    }
}
