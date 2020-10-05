<?php

declare(strict_types=1);

namespace App\Model;

use App\Http\Request\Room as RoomRequest;
use Carbon\Carbon;
use Hyperf\DbConnection\Model\Model;

/**
 * @method static Room|null find(int $id)
 * @method static Room findOrFail(int $id)
 * @property null|int id
 * @property null|string name
 * @property null|string avatar_url
 * @property null|Carbon created_at
 * @property null|Carbon updated_at
 */
class Room extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'room';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [];

    public static function fromRequest(RoomRequest $request): self
    {
        return (new self())
            ->setName($request->getName())
            ->setAvatarUrl($request->getAvatarUrl());
    }

    public function mergeWithRequest(RoomRequest $request): self
    {
        return $this
            ->setName($request->getName())
            ->setAvatarUrl($request->getAvatarUrl());
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): Room
    {
        $this->name = $name;
        return $this;
    }

    public function getAvatarUrl(): ?string
    {
        return $this->avatar_url;
    }

    public function setAvatarUrl(string $avatar_url): Room
    {
        $this->avatar_url = $avatar_url;
        return $this;
    }

    public function getCreatedAt(): ?Carbon
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): ?Carbon
    {
        return $this->updated_at;
    }
}
