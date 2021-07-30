<?php

namespace App\Models\Quizzes\Log;

use App\Models\Log;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

/**
 * Class Slouch
 *
 * @package App\Models\Quizzes\Log
 *
 * @property int    $id
 * @property string $result_id
 * @property int    $slouch_start
 * @property int    $slouch_count
 * @property int    $slouch_duration
 * @property int    $tilt_start
 * @property int    $tilt_count
 * @property int    $tilt_duration
 * @property int    $clock
 *
 * @method static Slouch find(int $id)
 * @method static Slouch findOrFail(int $id)
 *
 * @method static QueryBuilder ofResult(string $id)
 */
class Slouch extends Log
{
    protected $table = 'quizzes_logs_slouches_sec';

    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * Scope a query to specify sections of a given quiz ID.
     *
     * @param  QueryBuilder $query
     * @param  string       $resultId
     *
     * @return QueryBuilder
     */
    public function scopeOfResult($query, $resultId)
    {
        return $query->where('result_id', '=', $resultId);
    }
}