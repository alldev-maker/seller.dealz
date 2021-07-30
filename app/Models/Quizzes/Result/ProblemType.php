<?php

namespace App\Models\Quizzes\Result;

use stdClass as StdClass;
use App\Models\Quizzes\Result;
use App\Models\Model;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

/**
 * Class ProblemType
 *
 * @package App\Models\Quiz
 *
 * @property string $id
 * @property string $key
 * @property string $result_id
 * @property string $section_id
 * @property string $passage_id
 * @property string $question_id
 *
 * @method static ProblemType find(int $id)
 * @method static ProblemType findOrFail(int $id)
 *
 * @method static QueryBuilder ofResult(string $id)
 * @method static QueryBuilder ofSection(string $id)
 * @method static QueryBuilder ofPassage(string $id)
 * @method static QueryBuilder ofQuestion(string $id)
 */
class ProblemType extends Model
{

    const PATH = 'quizzes.results.sections.passages.questions.problemtypes';
    const SLUG = 'problemtype';

    protected $table = 'quizzes_results_problem_types';

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $fillable = [
        'name',
    ];

    protected $hidden = [
        'quiz_id',
        'section_id',
        'passage_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $appends = [

    ];

    /**
     * Scope a query to specify questions of a given quiz ID.
     *
     * @param  QueryBuilder  $query
     * @param  string        $resultId
     *
     * @return QueryBuilder
     */
    public function scopeOfResult($query, $resultId)
    {
        return $query->where('result_id', '=', $resultId);
    }

    /**
     * Scope a query to specify questions of a given section ID.
     *
     * @param  QueryBuilder  $query
     * @param  string        $sectionId
     *
     * @return QueryBuilder
     */
    public function scopeOfSection($query, $sectionId)
    {
        return $query->where('section_id', '=', $sectionId);
    }

    /**
     * Scope a query to specify questions of a given passage ID.
     *
     * @param  QueryBuilder  $query
     * @param  string        $passageId
     *
     * @return QueryBuilder
     */
    public function scopeOfPassage($query, $passageId)
    {
        return $query->where('passage_id', '=', $passageId);
    }

    /**
     * Scope a query to specify questions of a given question ID.
     *
     * @param  QueryBuilder  $query
     * @param  string        $questionId
     *
     * @return QueryBuilder
     */
    public function scopeOfQuestion($query, $questionId)
    {
        return $query->where('question_id', '=', $questionId);
    }
}
