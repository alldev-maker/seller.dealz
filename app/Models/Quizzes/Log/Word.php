<?php

namespace App\Models\Quizzes\Log;

use App\Models\Log;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

/**
 * Class Word
 *
 * @package App\Models\Quizzes\Log
 *
 * @property int    $id
 * @property string $result_id
 * @property string $passage_id
 * @property string $question_id
 * @property string $choice_id
 * @property int    $word_id
 * @property int    $word_text
 *
 * @method static Word find(int $id)
 * @method static Word findOrFail(int $id)
 *
 * @method static QueryBuilder ofResult(string $id)
 */
class Word extends Log
{
    protected $table = 'quizzes_logs_words';

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
     * Scope a query to specify sections of a given passage ID.
     *
     * @param  QueryBuilder $query
     * @param  string       $passageId
     *
     * @return QueryBuilder
     */
    public function scopeOfPassage($query, $passageId)
    {
        return $query->where('passage_id', '=', $passageId);
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

    /**
     * Scope a query to specify sections of a given choice ID.
     *
     * @param  QueryBuilder $query
     * @param  string       $choiceId
     *
     * @return QueryBuilder
     */
    public function scopeOfChoice($query, $choiceId)
    {
        return $query->where('choice_id', '=', $choiceId);
    }
}