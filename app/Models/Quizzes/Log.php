<?php

namespace App\Models\Quizzes;

use Carbon\Carbon;
use App\Models\Model;

/**
 * Class Log
 *
 * @package App\Models\Quizzes
 *
 * @property string $id
 *
 * @property Carbon $report_start
 * @property Carbon $report_end
 * @property int    $report_status
 *
 * @property Carbon $pulse_start
 * @property Carbon $pulse_end
 * @property int    $pulse_status
 *
 * @property Carbon $iris_start
 * @property Carbon $iris_end
 * @property int    $iris_status
 *
 * @property Carbon $emotion_start
 * @property Carbon $emotion_end
 * @property int    $emotion_status
 *
 * @property Carbon $blinks_start
 * @property Carbon $blinks_end
 * @property int    $blinks_status
 *
 * @property Carbon $slouch_start
 * @property Carbon $slouch_end
 * @property int    $slouch_status
 *
 * @property Carbon $respiratory_start
 * @property Carbon $respiratory_end
 * @property int    $respiratory_status
 *
 * @method static Log find(string $id)
 * @method static Log findOrFail(string $id)
 */
class Log extends Model
{

    const PATH = 'quizzes.logs';
    const SLUG = 'logs';

    protected $table = 'quizzes_logs';

    protected $casts = [
        'report_start' => 'datetime',
        'report_end'   => 'datetime',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
        'deleted_at'   => 'datetime',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $appends = [
        'duration',
        'urls',
    ];

    /**
     * An accessor method to return the URLs of the Log.
     *
     * Usage: $quiz->urls
     *
     * @return array
     */
    public function getUrlsAttribute()
    {
        $urls = [];

        return $urls;
    }

}