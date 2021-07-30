<?php

namespace App\Models\Quizzes\Quiz;

use App\Models\Audit\Loggable;
use App\Models\Quizzes\Types\Problem;
use DOMDocument;
use DOMNode;
use DOMXPath;
use stdClass as StdClass;
use App\Models\Quizzes\Quiz;
use App\Models\Model;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

/**
 * Class Question
 *
 * @package App\Models\Quiz
 *
 * @property string    $id
 * @property string    $question
 * @property StdClass  $type
 * @property int       $shuffle_choices
 * @property int       $difficulty
 * @property string    $explain_video
 * @property int       $points
 * @property string    $quiz_id
 * @property string    $section_id
 * @property string    $passage_id
 * @property int       $ordering
 * @property int       $passage_count
 * @property int       $read_count
 * @property string    $wrapped
 * @property int       $word_count
 *
 * @property Quiz      $quiz
 * @property Section   $section
 * @property Passage   $passage
 * @property Problem[] $problem_types
 * @property Choice[]  $choices
 * @property Choice[]  $choices_computed
 *
 * @method static Question find(int $id)
 * @method static Question findOrFail(int $id)
 *
 * @method static QueryBuilder|Question ofQuiz(string $id)
 * @method static QueryBuilder|Question ofSection(string $id)
 * @method static QueryBuilder|Question ofPassage(string $id)
 */
class Question extends Model
{
    use Loggable;

    const PATH = 'quizzes.quizzes.sections.passages.questions';
    const SLUG = 'question';

    const TYPE_MCSA = 1; // Multiple choice, single answer.
    const TYPE_MCMA = 2; // Multiple choice, multiple answers.
    const TYPE_TXTS = 3; // Text, short. Input field will be text box
    const TYPE_TXTL = 4; // Text, long. Input field will be textarea.
    const TYPE_ORDR = 5; // Order of answers. The correct order will earn a point.

    protected $table = 'quizzes_quizzes_questions';

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $fillable = [
        'question',
        'type',
        'points',
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
        'problem_types',
        'choices',
        'name',
        'wrapped',
        'urls',
    ];

    public $word_count = 0;

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
     * An accessor method to get the Quiz attribute.
     *
     * Usage: $question->quiz
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
            'random_questions',
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
     * An accessor method to get an array of Problem Types in a given Quiz.
     *
     * Usage: $quiz->problem_types
     *
     * @return Problem[]
     */
    public function getProblemTypesAttribute()
    {
        $problemsRs = Problem::join('quizzes_quizzes_problem_types', 'quizzes_types_problems.key', '=', 'quizzes_quizzes_problem_types.key')
            ->select('quizzes_types_problems.*')
            ->where('quizzes_quizzes_problem_types.question_id', '=', $this->id)
            ->get();

        $problems = [];

        /** @var Problem $problem */
        foreach ($problemsRs as $problem) {
            $problem->makeHidden(['description', 'urls']);
            $problem->makeVisible(['key', 'name', 'value']);
            $problems[] = $problem;
        }

        return $problems;
    }

    /**
     * An accessor method to get an array of Choices in a given Quiz.
     *
     * Usage: $quiz->choices
     *
     * @return Choice[]
     */
    public function getChoicesAttribute()
    {
        $choicesRs = Choice::ofQuestion($this->id)->orderByRaw('ordering ASC, choice ASC')->get();
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

    public function getNameAttribute()
    {
        $length = 50;
        $value  = str_replace(['<br>', '<br />', '</p><p>'], ' ', $this->question);
        $value  = strip_tags(html_entity_decode($value, null, 'UTF-8'));
        $value  = trim($value);

        if (strlen($value) > $length) {
            $value = explode("\n", wordwrap($value, $length));
            $value = $value[0] . '…';
        }

        return $value;
    }

    public function getWrappedAttribute()
    {
        $content = mb_convert_encoding($this->question, 'HTML-ENTITIES', mb_detect_encoding($this->question));
        $content = '<div class="dom-body">' . $content . '</div>';

        libxml_use_internal_errors(true);

        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->loadHTML($content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        $xpath = new DOMXPath($dom);
        $nodes = $xpath->evaluate("//div[@class=\"dom-body\"]");

        $this->traverseNodes($dom, $nodes[0], $this->id);

        $content = $dom->saveHTML();
        $content = preg_replace('/(\[span)(.*?)(\])(.*?)(\[\/span\])/', '<span $2>$4</span>', $content);

        return $content;
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

        }

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

    /**
     * @param  DOMDocument  $dom
     * @param  DOMNode      $node
     * @param  int          $question_id
     */
    protected function traverseNodes($dom, $node, $question_id)
    {
        if ($node->hasChildNodes()) {
            foreach ($node->childNodes as $childNode) {
                $this->traverseNodes($dom, $childNode, $question_id);
            }
        } else {
            if ($node->nodeType == XML_TEXT_NODE) {
                $text = $node->nodeValue;

                $pattern = '/\'?\w+([-\'’\x{02bb}]\w+)*\'?/u';
                $text    = preg_replace_callback(
                    $pattern,
                    function ($matches) use ($question_id) {
                        $this->word_count++;

                        return '[span data-question-id="' . $question_id . '" data-word-id="' . $this->word_count . '" class="s-text"]' . $matches[0] . '[/span]';
                    },
                    $text
                );

                $node->nodeValue = $text;
            }
        }
    }
}