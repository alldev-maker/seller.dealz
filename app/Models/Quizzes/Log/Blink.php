<?php

namespace App\Models\Quizzes\Log;

use App\Models\Log;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

/**
 * Class Blink
 *
 * @package App\Models\Quizzes\Log
 *
 * @property int    $id
 * @property string $result_id
 * @property int    $start
 * @property int    $count
 * @property int    $duration
 * @property int    $clock
 *
 * @method static Blink find(int $id)
 * @method static Blink findOrFail(int $id)
 *
 * @method static QueryBuilder ofResult(string $id)
 */
class Blink extends Log
{
    protected $table = 'quizzes_logs_blinks_sec';

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