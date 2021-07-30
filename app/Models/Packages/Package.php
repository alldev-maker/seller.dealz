<?php

namespace App\Models\Packages;

use App\Models\Model;

/**
 * Class Package
 *
 * @package App\Models\Packages
 *
 * @property string $id
 * @property string $slug
 * @property string $name
 * @property string $description
 *
 * @property int opt_right_wrong
 * @property int opt_adjusted_score
 * @property int opt_time_per_question
 * @property int opt_pulse_rate
 * @property int opt_eye_dilation
 * @property int opt_eye_tracking
 * @property int opt_video_tracking
 *
 */
class Package extends Model
{
    const PATH = 'packages.packages';
    const SLUG = 'package';

    protected $table = 'packages_packages';

    protected $fillable = [
        'slug',
        'name',
        'description',
        'opt_right_wrong',
        'opt_adjusted_score',
        'opt_time_per_question',
        'opt_pulse_rate',
        'opt_eye_dilation',
        'opt_eye_tracking',
        'opt_video_tracking',
    ];

    protected $appends = ['urls'];

    public function getUrlsAttribute()
    {
        $urls         = [];
        $urls['edit'] = route(sprintf('%s.edit', self::PATH), ['id' => $this->id]);

        return $urls;
    }
}
