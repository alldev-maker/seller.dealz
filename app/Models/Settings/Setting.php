<?php

namespace App\Models\Settings;

use \App\Models;

/**
 * Class Setting
 *
 * @package App\Models\Settings
 *
 * @property string $key
 * @property mixed  $value
 * @property string $datatype
 *
 * @method static Setting|null find(string $key)
 * @method static Setting|null findOrFail(string $key)
 */
class Setting extends Models\Model
{
    protected $table = 'settings_settings';

    protected $primaryKey = 'key';

    public $incrementing = false;
    public $keyType      = 'string';

    public function getValueAttribute($value)
    {
        switch ($this->datatype) {
            case 'string':
            default:
                return $value;

            case 'int':
                return (int) $value;

            case 'double':
                return (double) $value;

            case 'float':
                return (float) $value;

            case 'CSV':
                return str_getcsv($value);

            case 'Role':
                if (empty($value)) {
                    $role       = new Models\Admin\Role();
                    $role->id   = '';
                    $role->slug = '';
                    $role->name = '';
                } else {
                    $role = Models\Admin\Role::find($value);
                    $role->makeHidden(['description', 'urls']);
                }

                return $role;
        }
    }

}
