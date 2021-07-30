<?php

namespace App\Models\Quizzes\Result;

use App\Models\Quizzes\Quiz;
use App\Models\Model;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

/**
 * Class Section
 *
 * @package App\Models\Result
 *
 * @property string $id
 * @property string $name
 * @property string $description
 * @property int    $total
 * @property int    $score
 * @property int    $questions_total
 * @property int    $questions_answered
 * @property int    $questions_skipped
 * @property int    $answers_correct
 * @property int    $answers_incorrect
 * @property string $result_id
 * @property int    $ordering
 * @property string $type_id
 * @property string $section_id
 * @property int    $time_limit
 * @property array  $blocks Answer strip blocks.
 *
 * @property Quiz   $quiz
 *
 * @method static Section find(int $id)
 * @method static Section findOrFail(int $id)
 *
 * @method static Section[] get()
 *
 * @method static QueryBuilder|Section ofResult(string $id)
 */
class Section extends Model
{

    const PATH = 'quizzes.results.sections';
    const SLUG = 'section';

    protected $table = 'quizzes_results_sections';

    protected $casts = [
        'enabled_until' => 'datetime',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
        'deleted_at'    => 'datetime',
    ];

    protected $fillable = [
        'name',
        'description',
        'ordering',
    ];

    protected $hidden = [
        'user_id',
        'quiz_id',
        'section_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $appends = [
        'quiz',
        'urls',
    ];

    /**
     * Scope a query to specify sections of a given quiz ID.
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

    /**
     * An accessor method to get the Quiz attribute.
     *
     * Usage: $section->quiz
     *
     * @return Quiz
     */
    public function getResultAttribute()
    {
        $quiz = Quiz::find($this->result_id);

        if (!$quiz) {
            $quiz = new Quiz();

            $quiz->name = '';
        }

        $hideFields = [
            'description',
            'sections',
            'urls',
        ];

        $quiz->makeHidden($hideFields);

        return $quiz;
    }

    /**
     * An accessor method to return the URLs of the Section.
     *
     * Usage: $section->urls
     *
     * @return array
     */
    public function getUrlsAttribute()
    {
        $urls = [];

        $urls['passages'] = route(sprintf('%s.passages.index', self::PATH), ['id' => $this->result_id, 'sid' => $this->id]);

        return $urls;
    }

}