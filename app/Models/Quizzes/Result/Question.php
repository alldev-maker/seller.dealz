<?php

namespace App\Models\Quizzes\Result;

use stdClass as StdClass;
use App\Models\Quizzes\Result;
use App\Models\Model;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

/**
 * Class Question
 *
 * @package App\Models\Quizzes\Result
 *
 * @property string   $id
 * @property int      $number
 * @property string   $question
 * @property string   $question_html
 * @property StdClass $type
 * @property int      $difficulty
 * @property int      $explain_video
 * @property int      $points
 * @property int      $score
 * @property string   $result_id
 * @property string   $section_id
 * @property string   $passage_id
 * @property string   $question_id
 * @property int      $ordering
 * @property int      $read_count
 * @property int      $passage_count
 *
 * @property Result   $result
 * @property Choice[] $choices
 *
 * @method static Question find(int $id)
 * @method static Question findOrFail(int $id)
 *
 * @method static Question[] get()
 *
 * @method static QueryBuilder|Question ofQuiz(string $id)
 * @method static QueryBuilder|Question ofResult(string $id)
 * @method static QueryBuilder|Question ofSection(string $id)
 * @method static QueryBuilder|Question ofPassage(string $id)
 */
class Question extends Model
{

    const PATH = 'quizzes.results.sections.passages.questions';
    const SLUG = 'question';

    const TYPE_MCSA = 1; // Multiple choice, single answer.
    const TYPE_MCMA = 2; // Multiple choice, multiple answers.
    const TYPE_TXTS = 3; // Text, short. Input field will be text box
    const TYPE_TXTL = 4; // Text, long. Input field will be textarea.
    const TYPE_ORDR = 5; // Order of answers. The correct order will earn a point.

    protected $table = 'quizzes_results_questions';

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $fillable = [
        'question',
        'type',
        'points',
        'score',
        'difficulty',
        'ordering',
    ];

    protected $hidden = [
        'user_id',
        'quiz_id',
        'section_id',
        'passage_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $appends = [
        'passage',
        'section',
        'choices',
        'answer',
        'correct',
        'name',
        'urls',
    ];

    /**
     * Scope a query to specify questions of a given result ID.
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
     * An accessor method to get the Quiz attribute.
     *
     * Usage: $question->result
     *
     * @return Result
     */
    public function getResultAttribute()
    {
        $result = Result::find($this->result_id);

        if (!$result) {
            $result = new Result();
        }

        $hideFields = [
            'description',
            'enabled',
            'enabled_until',
            'duration',
            'auto_submit',
            'random_questions',
            'random_questions',
            'random_choices',
            'taken_count',
            'user',
            'sections',
            'urls',
        ];

        $result->makeHidden($hideFields);

        return $result;
    }

    /**
     * An accessor method to get the Section attribute.
     *
     * Usage: $question->section
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
            'questions',
            'urls',
        ];

        $section->makeHidden($hideFields);

        return $section;
    }

    /**
     * An accessor method to get the Passage attribute.
     *
     * Usage: $question->passage
     *
     * @return Passage
     */
    public function getPassageAttribute()
    {
        $passage = Passage::find($this->passage_id);

        if (!$passage) {
            $passage = new Passage();

            $passage->name = '';
        }

        $hideFields = [
            'description',
            'content',
            'questions',
            'urls',
        ];

        $passage->makeHidden($hideFields);

        return $passage;
    }

    /**
     * An accessor method to get an array of Choices in a given Result.
     *
     * Usage: $quiz->sections
     *
     * @return Choice[]
     */
    public function getChoicesAttribute()
    {
        $choicesRs = Choice::ofQuestion($this->id)->orderByRaw('ordering ASC, choice_name ASC')->get();
        $choices   = [];

        /** @var Choice $choice */
        $letter = 'A';
        foreach ($choicesRs as $choice) {
            $choice->makeVisible(['id', 'choice', 'is_correct', 'points', 'ordering']);
            $choice->letter = $letter;
            $choices[]      = $choice;

            $letter++;
        }
        return $choices;
    }

    /**
     * An accessor method to get an array of Answers in a given Question.
     *
     * Usage: $quiz->answer
     *
     * @return Answer
     */
    public function getAnswerAttribute()
    {
        /** @var Answer $answer */
        $answer = Answer::ofQuestion($this->id)->orderByRaw('ordering ASC')->first();

        return $answer;
    }

    /**
     * An accessor method to get an array of Answers in a given Question.
     *
     * Usage: $quiz->correct
     *
     * @return Choice
     */
    public function getCorrectAttribute()
    {
        /** @var Choice $choice */
        $choice = Choice::correct($this->id)->first();

        return $choice;
    }

    public function getNameAttribute()
    {
        $length = 50;
        $value  = strip_tags(html_entity_decode($this->question, null, 'UTF-8'));

        if (strlen($value) > $length) {
            $value = explode("\n", wordwrap($value, $length));
            $value = $value[0].'…';
        }

        return $value;
    }

    public function getTypeAttribute($value)
    {
        $class = new StdClass();
        switch ($value) {
            case self::TYPE_MCSA:
            default:
                $class->id   = self::TYPE_MCSA;
                $class->name = 'Multiple choice, single answer';
                break;
            case self::TYPE_MCMA:
                $class->id   = self::TYPE_MCMA;
                $class->name = 'Multiple choice, multiple answers';
                break;
            case self::TYPE_TXTS:
                $class->id   = self::TYPE_TXTS;
                $class->name = 'Text, short';
                break;
            case self::TYPE_TXTL:
                $class->id   = self::TYPE_TXTL;
                $class->name = 'Text, long';
                break;
            case self::TYPE_ORDR:
                $class->id   = self::TYPE_ORDR;
                $class->name = 'Order of answers';
                break;
        };

        return $class;
    }

    public function setTypeAttribute($value)
    {
        if (is_int($value)) {
            $this->attributes['type'] = $value;
        } else {
            if (is_object($value)) {
                $this->attributes['type'] = isset($value->id) ? $value->id : 0;
            } else {
                if (is_string($value)) {
                    $this->attributes['type'] = (int) $value;
                }
            }
        }
    }

    /**
     * An accessor method to list the URLs for managing the Question.
     *
     * Usage: $question->urls
     *
     * @return array
     */
    public function getUrlsAttribute()
    {
        $urls = [];

        return $urls;
    }

}