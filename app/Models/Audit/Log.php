<?php

namespace App\Models\Audit;

use App\Models\Admin\User;
use App\Models\Model;

/**
 * Class User
 *
 * @package App\Models\Audit
 *
 * @property int    $id
 * @property string $user_id
 * @property string $event
 * @property string $model
 * @property string $model_id
 * @property string values_old
 * @property string values_new
 * @property string url
 * @property string ip_address
 * @property string user_agent
 *
 * @property User   $user
 */
class Log extends Model
{
    const PATH = 'audit.logs';
    const SLUG = 'log';

    protected $table = 'audit_logs';

    protected $hidden = [
        'user_id',
        'created_at',
        'updated_at',
    ];

    protected $appends = [
        'user',
    ];

    /**
     * An accessor method to get the User attribute.
     *
     * Usage: $log->user
     *
     * @return User
     */
    public function getUserAttribute()
    {
        $user = User::find($this->user_id);

        if (!$user) {
            $user = new User();

            $user->name = '';
        }

        $user->makeHidden(['role_id', 'role', 'notes', 'urls', 'device_id', 'app_id', 'app_token']);

        return $user;
    }
}