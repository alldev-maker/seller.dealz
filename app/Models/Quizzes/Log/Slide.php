<?php

namespace App\Models\Quizzes\Log;

use App\Models\Log;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

/**
 * Class Slide
 *
 * @package App\Models\Quizzes\Log
 *
 * @property int    $id
 * @property string $result_id
 * @property string $type
 * @property string $reference_id
 * @property int    $answered
 * @property int    $passage_reread
 * @property int    $clock
 * @property int    $duration
 *
 * @method static Slide find(int $id)
 * @method static Slide findOrFail(int $id)
 *
 * @method static Slide[] get();
 *
 * @method static QueryBuilder|Slide ofResult(string $id)
 * @method static QueryBuilder|Slide ofType(string $id)
 * @method static QueryBuilder|Slide ofReference(string $id)
 * @method static QueryBuilder|Slide ofQuestion(string $id)
 */
class Slide extends Log
{
    protected $table = 'quizzes_logs_slides';

    protected $casts = [
        'data'       => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Scope a query to specify sections of a given result ID.
     *
     * @param  QueryBuilder  $query
     * @param  string        $resultId
     *
     * @return QueryBuilder|Slide
     */
    public function scopeOfResult($query, $resultId)
    {
        return $query->where('result_id', '=', $resultId);
    }

    /**
     * Scope a query to specify sections of a given type.
     *
     * @param  QueryBuilder  $query
     * @param  string        $type
     *
     * @return QueryBuilder|Slide
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', '=', $type);
    }

    /**
     * Scope a query to specify there reference ID. Can be a Question ID or Passage ID,
     * depending on the type.
     *
     * @param  QueryBuilder  $query
     * @param  string        $reference_id
     *
     * @return QueryBuilder|Slide
     */
    public function scopeOfReference($query, $reference_id)
    {
        return $query->where('reference_id', '=', $reference_id);
    }

    /**
     * Scope a query to specify sections of a given question ID.
     *
     * @param  QueryBuilder  $query
     * @param  string        $questionId
     *
     * @return QueryBuilder|Slide
     */
    public function scopeOfQuestion($query, $questionId)
    {
        return $query->where('reference_id', '=', $questionId);
    }
}
