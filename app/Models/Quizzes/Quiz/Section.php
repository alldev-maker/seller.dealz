<?php

namespace App\Models\Quizzes\Quiz;

use App\Models\Audit\Loggable;
use App\Models\Quizzes\Quiz;
use App\Models\Model;
use App\Models\Quizzes\Types\Section as Type;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;


/**
 * Class Section
 *
 * @package App\Models\Quiz
 *
 * @property string    $id
 * @property string    $name
 * @property string    $description
 * @property string    $quiz_id
 * @property string    $type_id
 * @property int       $time_limit
 * @property int       $ordering
 *
 * @property Quiz      $quiz
 * @property Type      $type
 * @property Passage[] $passages
 *
 * @method static Section find(string $id)
 * @method static Section findOrFail(string $id)
 *
 * @method static Section[] get()
 *
 * @method static QueryBuilder|Section ofQuiz(string $id)
 */
class Section extends Model
{
    use Loggable;

    const PATH = 'quizzes.quizzes.sections';
    const SLUG = 'section';

    protected $table = 'quizzes_quizzes_sections';

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
        'type_id',
        'section_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $appends = [
        'type',
        'urls',
        'passages',
    ];

    /**
     * @var Passage[]
     */
    protected $passages  = [];

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
     * An accessor method to get the Quiz attribute.
     *
     * Usage: $section->quiz
     *
     * @return Quiz
     */
    public function getQuizAttribute()
    {
        $quiz = Quiz::find($this->quiz_id);

        if (!$quiz) {
            $quiz = new Quiz();

            $quiz->name = '';
        }

        $hideFields = [
            'description',
            'enabled',
            'enabled_until',
            'duration',
            'auto_submit',
            'random_passages',
            'random_questions',
            'random_choices',
            'taken_count',
            'user',
            'sections',
            'urls',
        ];

        $quiz->makeHidden($hideFields);

        return $quiz;
    }

    /**
     * An accessor method to get the Type attribute.
     *
     * Usage: $section->type
     *
     * @return Type
     */
    public function getTypeAttribute()
    {
        $type = Type::find($this->type_id);

        if (!$type) {
            $type       = new Type();
            $type->name = '';
        }

        $hideFields = [
            'description',
            'urls',
        ];

        $type->makeHidden($hideFields);

        return $type;
    }

    /**
     * @return Passage[]
     */
    public function getPassagesAttribute()
    {
        return $this->passages;
    }

    /**
     * @param Passage[] $value
     */
    public function setPassagesAttribute($value)
    {
        $this->passages = $value;
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

        $urls['passages'] = route(sprintf('%s.passages.index', self::PATH), ['id' => $this->quiz_id, 'sid' => $this->id]);

        return $urls;
    }

}