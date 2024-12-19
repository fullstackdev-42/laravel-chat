<?php

namespace App\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Str;

/**
 * App\Models\Group
 *
 * @property string $id
 * @property string $name
 * @property string|null $description
 * @property string $photo_url
 * @property int $privacy
 * @property int $group_type 1 => Open (Anyone can send message), 2 => Close (Only Admin can send message)
 * @property int $created_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-write mixed $raw
 * @property-read Collection|User[] $users
 * @property-read int|null $users_count
 *
 * @method static Builder|Group newModelQuery()
 * @method static Builder|Group newQuery()
 * @method static Builder|Group query()
 * @method static Builder|Group whereCreatedAt($value)
 * @method static Builder|Group whereCreatedBy($value)
 * @method static Builder|Group whereDescription($value)
 * @method static Builder|Group whereGroupType($value)
 * @method static Builder|Group whereId($value)
 * @method static Builder|Group whereName($value)
 * @method static Builder|Group wherePhotoUrl($value)
 * @method static Builder|Group wherePrivacy($value)
 * @method static Builder|Group whereUpdatedAt($value)
 * @mixin Eloquent
 *
 * @property-read User $createdByUser
 * @property-read mixed $group_created_by
 * @property-read mixed $my_role
 * @property-read Collection|LastConversation[] $lastConversations
 * @property-read int|null $last_conversations_count
 * @property-read Collection|User[] $usersWithTrashed
 * @property-read int|null $users_with_trashed_count
 * @property int $is_default
 *
 * @method static Builder|Group whereIsDefault($value)
 */
class Center extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'code', 'remark',
    ];

    public static $PATH = 'centers';

    protected $guarded = [];

    public function getKeyType()
    {
        return 'string';
    }

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'string',
        'name' => 'string',
        'code' => 'string',
        'remark' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required|string|max:100',
        'code' => 'required|string|max:20',
        'remark' => 'nullable|string',
    ];

    /**
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'center_users', 'center_id', 'user_id')->withPivot(['role', 'created_at'])->orderByDesc('role')->orderBy('users.name', 'asc');
    }

    /**
     * Get all of the groups for the Center
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function groups(): HasMany
    {
        return $this->hasMany(Group::class, 'center_id', 'id');
    }
}
