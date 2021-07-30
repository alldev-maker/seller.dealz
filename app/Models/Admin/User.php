<?php

namespace App\Models\Admin;

use App\Models\Model;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Notifications\Notifiable;

/**
 * Class User
 *
 * @package App\Models\Users
 *
 * @property string $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $remember_token
 * @property string $device_id
 * @property string $app_id
 * @property string $app_token
 * @property string $nice_name
 * @property string $role_id
 * @property string $notes
 *
 * @property Role   $role
 *
 * @method static Builder ofRole(Role $role)
 */
class User extends Model implements AuthorizableContract, AuthenticatableContract, CanResetPasswordContract, MustVerifyEmailContract
{
    use Authenticatable, Authorizable, CanResetPassword, MustVerifyEmail, Notifiable;

    const PATH = 'admin.users';
    const SLUG = 'user';

    protected $table = 'admin_users';

    protected $rememberTokenName = 'remember_token';

    protected $fillable = [
        'name',
        'password',
        'email',
        'device_id',
        'app_id',
        'app_token',
        'role_id',
        'nice_name',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'role_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'created_at'        => 'datetime',
        'updated_at'        => 'datetime',
        'deleted_at'        => 'datetime',
    ];

    protected $appends = [
        'role',
        'urls',
    ];

    /**
     * @param  Builder  $query
     * @param  Role     $role
     *
     * @return mixed
     */
    public function scopeOfRole($query, $role)
    {
        return $query->where('role_id', $role->id);
    }

    public function getRoleAttribute()
    {
        $role = Role::find($this->role_id);

        if ($role) {
            $role->makeHidden(['description', 'urls']);
        }

        return $role;
    }

    public function getUrlsAttribute()
    {
        $urls = [];

        if (!empty($this->id)) {
            $urls['edit'] = route(sprintf('%s.edit', self::PATH), ['id' => $this->id]);
        }

        return $urls;
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function isDeveloper()
    {
        return $this->hasRole('developer');
    }

    public function authorizeRoles($roles)
    {
        if ($this->hasAnyRole($roles)) {
            return true;
        }

        abort(401, 'This action is unauthorized.');

        return false;
    }

    public function hasAnyRole($roles)
    {
        if (is_array($roles)) {
            foreach ($roles as $role) {
                if ($this->hasRole($role)) {
                    return true;
                }
            }
        } else {
            if ($this->hasRole($roles)) {
                return true;
            }
        }
        return false;
    }

    public function hasRole($role)
    {
        if ($this->role()->where('slug', $role)->first()) {
            return true;
        }
        return false;
    }

}