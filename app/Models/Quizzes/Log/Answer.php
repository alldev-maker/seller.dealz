<?php

namespace App\Models\Quizzes\Log;

use App\Models\Log;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

/**
 * Class Answer
 *
 * @package App\Models\Quizzes\Log
 *
 * @property int    $id
 * @property string $result_id
 * @property string $question_id
 * @property string $choice_id
 * @property string $choice_name
 * @property string $letter
 * @property int    $clock
 *
 * @method static Answer find(int $id)
 * @method static Answer findOrFail(int $id)
 *
 * @method static QueryBuilder ofResult(string $id)
 */
class Answer extends Log
{
    protected $table = 'quizzes_logs_answers';

    protected $casts = [
        'data'       => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Scope a query to specify sections of a given result ID.
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

    /**
     * Scope a query to specify sections of a given question ID.
     *
     * @param  QueryBuilder $query
     * @param  string       $questionId
     *
     * @return QueryBuilder
     */
    public function scopeOfQuestion($query, $questionId)
    {
        return $query->where('question_id', '=', $questionId);
    }
}