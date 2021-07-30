<?php

namespace App\Models\Quizzes\Result;

use App\Models\Quizzes\Quiz;
use App\Models\Model;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

/**
 * Class Passage
 *
 * @package App\Models\Quiz
 *
 * @property string $id
 * @property string $name
 * @property string $description
 * @property string $content
 * @property string $content_html
 * @property string $result_id
 * @property string $section_id
 * @property string $passage_id
 * @property int    $ordering
 *
 * @property Quiz   $quiz
 *
 * @method static Passage[] get()
 * @method static Passage find(int $id)
 * @method static Passage findOrFail(int $id)
 *
 * @method static QueryBuilder ofResult(string $id)
 * @method static QueryBuilder ofSection(string $id)
 */
class Passage extends Model
{

    const PATH = 'quizzes.results.sections.passages';
    const SLUG = 'passage';

    protected $table = 'quizzes_results_passages';

    protected $casts = [
        'enabled_until' => 'datetime',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
        'deleted_at'    => 'datetime',
    ];

    protected $fillable  = [
        'name',
        'description',
        'content',
        'ordering',
    ];

    protected $hidden = [
        'user_id',
        'quiz_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $appends = [
        'section',
        'urls',
    ];

    /**
     * Scope a query to specify passages of a given quiz ID.
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
     * Scope a query to specify passages of a given section ID.
     *
     * @param  QueryBuilder $query
     * @param  string       $sectionId
     *
     * @return QueryBuilder
     */
    public function scopeOfSection($query, $sectionId)
    {
        return $query->where('section_id', '=', $sectionId);
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
     * An accessor method to get the Section attribute.
     *
     * Usage: $passage->section
     *
     * @return Section
     */
    public function getSectionAttribute()
    {
        $section = Section::find($this->section_id);

        if (!$section) {
            $section = new Section();

            $section->name = '';
        }

        $hideFields = [
            'description',
            'user',
            'passages',
            'urls'
        ];

        $section->makeHidden($hideFields);

        return $section;
    }

    /**
     * An accessor method to list the URLs for managing the Passage.
     *
     * Usage: $passage->urls
     *
     * @return array
     */
    public function getUrlsAttribute()
    {
        $urls = [];

        $urls['questions'] = route(sprintf('%s.questions.index', self::PATH), ['id' => $this->quiz_id, 'sid' => $this->section_id, 'pid' => $this->id]);

        return $urls;
    }

}