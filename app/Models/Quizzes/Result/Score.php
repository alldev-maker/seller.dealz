<?php

namespace App\Models\Quizzes\Result;

use App\Models\Model;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

/**
 * Class Score
 *
 * @package App\Models\Result
 *
 * @property string $id
 * @property string $result_id
 * @property string $name
 * @property int    $score_raw
 * @property int    $score_raw_total
 * @property int    $score_scaled
 * @property int    $score_percent
 *
 * @method static Score find(int $id)
 * @method static Score findOrFail(int $id)
 *
 * @method static Score[] get()
 *
 * @method static QueryBuilder|Score ofResult(string $id)
 */
class Score extends Model
{

    const PATH = 'quizzes.results.scores';
    const SLUG = 'score';

    protected $table = 'quizzes_results_scores';

    protected $casts = [
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
        'deleted_at'    => 'datetime',
    ];

    protected $fillable = [
        'name',
    ];

    protected $hidden = [
        'result_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Scope a query to specify sections of a given Result ID.
     *
     * @param  QueryBuilder  $query
     * @param  string        $result_id
     *
     * @return QueryBuilder
     */
    public function scopeOfResult($query, $result_id)
    {
        return $query->where('result_id', '=', $result_id);
    }

}