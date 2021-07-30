<?php

namespace App\Models\Quizzes\Quiz;

use stdClass as StdClass;
use App\Models\Quizzes\Quiz;
use App\Models\Model;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

/**
 * Class ProblemType
 *
 * @package App\Models\Quiz
 *
 * @property string $id
 * @property string $key
 * @property string $quiz_id
 * @property string $section_id
 * @property string $passage_id
 * @property string $question_id
 *
 * @method static ProblemType|null find(string $id)
 * @method static ProblemType findOrFail(int $id)
 *
 * @method static QueryBuilder|ProblemType ofQuiz(string $id)
 * @method static QueryBuilder|ProblemType ofSection(string $id)
 * @method static QueryBuilder|ProblemType ofPassage(string $id)
 * @method static QueryBuilder|ProblemType ofQuestion(string $id)
 */
class ProblemType extends Model
{

    const PATH = 'quizzes.quizzes.sections.passages.questions.problemtypes';
    const SLUG = 'problemtype';

    protected $table = 'quizzes_quizzes_problem_types';

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
        'question_id',
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
     * @param  string        $quizId
     *
     * @return QueryBuilder
     */
    public function scopeOfQuiz($query, $quizId)
    {
        return $query->where('quiz_id', '=', $quizId);
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
