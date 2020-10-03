<?php

declare(strict_types=1);

namespace App\Model;

use Carbon\Carbon;
use Hyperf\Database\Model\Concerns\HasTimestamps;
use Hyperf\Database\Model\Relations\HasMany;
use Hyperf\DbConnection\Model\Model;
use HyperfExt\Auth\Authenticatable;
use HyperfExt\Auth\Contracts\AuthenticatableInterface;
use HyperfExt\Jwt\Contracts\JwtSubjectInterface;

/**
 * @method static User|null find(int $id)
 * @method static User findOrFail(int $id)
 * @property int|null id
 * @property string|null password
 * @property string|null remember_token
 * @property Carbon|null created_at
 * @property Carbon|null updated_at
 */
class User extends Model implements AuthenticatableInterface, JwtSubjectInterface
{
    use HasTimestamps;
    use Authenticatable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user';

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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): User
    {
        $this->password = $password;
        return $this;
    }

    public function getRememberToken(): ?string
    {
        return $this->remember_token;
    }

    public function setRememberToken(string $remember_token): User
    {
        $this->remember_token = $remember_token;
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

    public function getJwtIdentifier(): ?int
    {
        return $this->id;
    }

    public function getJwtCustomClaims(): array
    {
        return [];
    }

    public function socialAccounts(): HasMany
    {
        return $this->hasMany(SocialAccount::class);
    }
}
