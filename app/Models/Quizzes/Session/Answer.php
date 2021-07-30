<?php

namespace App\Models\Quizzes\Session;

use App\Models\Log;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

/**
 * Class Answer
 *
 * @package App\Models\Quizzes\Session
 *
 * @property int    $id
 * @property string $session_id
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
    protected $table = 'quizzes_sessions_answers';

    protected $casts = [
        'data'       => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Scope a query to specify sections of a given result ID.
     *
     * @param  QueryBuilder $query
     * @param  string       $sessionId
     *
     * @return QueryBuilder
     */
    public function scopeOfSession($query, $sessionId)
    {
        return $query->where('session_id', '=', $sessionId);
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