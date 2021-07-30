<?php

namespace App\Models\Admin;

use stdClass as StdClass;
use App\Models\Model;

/**
 * Class Role
 *
 * @package App\Models\Admin
 *
 * @property string $id
 * @property string $slug
 * @property string $name
 * @property string $description
 * @property string $prefix
 */
class Role extends Model
{
    const PATH = 'admin.roles';
    const SLUG = 'role';

    protected $table = 'admin_roles';

    protected $fillable = [
        'slug',
        'name',
        'description',
    ];

    protected $appends = ['urls'];

    public function getUrlsAttribute()
    {
        $urls         = [];
        $urls['edit'] = route(sprintf('%s.edit', self::PATH), ['id' => $this->id]);

        return $urls;
    }
}
