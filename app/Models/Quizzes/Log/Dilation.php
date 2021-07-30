<?php

namespace App\Models\Quizzes\Log;

use App\Models\Log;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

/**
 * Class Dilation
 *
 * @package App\Models\Quizzes\Log
 *
 * @property int    $id
 * @property string $result_id
 * @property int    $width
 * @property int    $height
 * @property int    $blinks
 * @property int    $clock
 *
 * @method static Dilation find(int $id)
 * @method static Dilation findOrFail(int $id)
 *
 * @method static QueryBuilder ofResult(string $id)
 */
class Dilation extends Log
{
    protected $table = 'quizzes_logs_irises_sec';

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