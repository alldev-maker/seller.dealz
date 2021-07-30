<?php

namespace App\Models\Geography;

use App\Models\Model;


/**
 * Class Country
 *
 * @package App\Models\Geography
 *
 * @property string $id
 * @property string $slug
 * @property string $name_common
 * @property string $name_official
 */
class Country extends Model
{
    const PATH = 'geography.countries';
    const SLUG = 'country';

    protected $table = 'geography_countries';

    protected $fillable = [
        'slug',
        'name_common',
        'name_official',
    ];

    protected $appends = ['urls'];

    public function getUrlsAttribute()
    {
        if (!empty($this->id)) {
            return [
                'edit' => route(sprintf('%s.edit', self::PATH), ['id' => $this->id]),
            ];
        } else {
            return [
                'edit' => '',
            ];
        }
    }
}
