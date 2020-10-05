<?php

declare(strict_types=1);

namespace App\Model;

use Carbon\Carbon;
use Hyperf\Database\Model\Relations\BelongsTo;
use Hyperf\DbConnection\Model\Model;

/**
 * @method static SocialAccount|null find(int $id)
 * @method static SocialAccount findOrFail(int $id)
 * @property null|int id
 * @property null|int user_id
 * @property null|int type
 * @property null|string social_id
 * @property null|string email
 * @property null|string avatar_url
 * @property null|Carbon created_at
 * @property null|Carbon updated_at
 */
class SocialAccount extends Model
{
    public const TYPE_GITHUB = 1;

    public const TYPE_GOOGLE = 2;

    public const TYPES = [
        self::TYPE_GITHUB,
        self::TYPE_GOOGLE,
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'social_account';

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

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = ['user'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function setUserId(int $user_id): SocialAccount
    {
        $this->user_id = $user_id;
        return $this;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): SocialAccount
    {
        $this->type = $type;
        return $this;
    }

    public function getSocialId(): ?string
    {
        return $this->social_id;
    }

    public function setSocialId(string $social_id): SocialAccount
    {
        $this->social_id = $social_id;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): SocialAccount
    {
        $this->email = $email;
        return $this;
    }

    public function getAvatarUrl(): ?string
    {
        return $this->avatar_url;
    }

    public function setAvatarUrl(string $avatar_url): SocialAccount
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

    public function isGithubAccount(): bool
    {
        return $this->type === self::TYPE_GITHUB;
    }
}
