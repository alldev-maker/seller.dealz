<?php

namespace App\Models\Quizzes\Log;

use App\Models\Log;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

/**
 * Class Pulse
 *
 * @package App\Models\Quizzes\Log
 *
 * @property int    $id
 * @property string $result_id
 * @property int    $rate
 * @property int    $clock
 *
 * @method static Pulse find(int $id)
 * @method static Pulse findOrFail(int $id)
 * @method static Pulse first()
 *
 * @method static QueryBuilder ofResult(string $id)
 */
class Pulse extends Log
{
    protected $table = 'quizzes_logs_pulses_sec';

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