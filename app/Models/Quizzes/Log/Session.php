<?php

namespace App\Models\Quizzes\Log;

use App\Models\Log;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

/**
 * Class Session
 *
 * @package App\Models\Quizzes\Log
 *
 * @property int    $id
 * @property string $result_id
 * @property string $content
 *
 * @method static Session find(int $id)
 * @method static Session findOrFail(int $id)
 *
 * @method static QueryBuilder ofResult(string $id)
 */
class Session extends Log
{
    protected $table = 'quizzes_logs_sessions';

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