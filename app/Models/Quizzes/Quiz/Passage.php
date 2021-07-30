<?php

namespace App\Models\Quizzes\Quiz;

use App\Models\Audit\Loggable;
use App\Models\Quizzes\Quiz;
use App\Models\Model;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;


/**
 * Class Passage
 *
 * @package App\Models\Quiz
 *
 * @property string     $id
 * @property string     $name
 * @property string     $description
 * @property string     $qa_layout
 * @property string     $content
 * @property string     $quiz_id
 * @property string     $section_id
 * @property int        $ordering
 * @property int        $read_count
 * @property string     $wrapped
 * @property int        $word_count
 *
 * @property Quiz       $quiz
 * @property Question[] $questions
 *
 * @method static Passage find(int $id)
 * @method static Passage findOrFail(int $id)
 *
 * @method static Passage[] get()
 *
 * @method static QueryBuilder|Passage ofQuiz(string $id)
 * @method static QueryBuilder|Passage ofSection(string $id)
 */
class Passage extends Model
{
    use Loggable;

    const PATH = 'quizzes.quizzes.sections.passages';
    const SLUG = 'passage';

    protected $table = 'quizzes_quizzes_passages';

    protected $casts = [
        'enabled_until' => 'datetime',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
        'deleted_at'    => 'datetime',
    ];

    protected $fillable = [
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
        'questions',
        'urls',
    ];

    public $word_count = 0;

    /**
     * Scope a query to specify passages of a given quiz ID.
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
     * Scope a query to specify passages of a given section ID.
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
     * An accessor method to get the Quiz attribute.
     *
     * Usage: $passage->quiz
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
            'urls',
        ];

        $section->makeHidden($hideFields);

        return $section;
    }

    public function getWrappedAttribute()
    {
        $content = mb_convert_encoding($this->content, 'HTML-ENTITIES', mb_detect_encoding($this->content));
        $content = '<div class="dom-body">' . $content . '</div>';

        libxml_use_internal_errors(true);

        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->loadHTML($content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        $xpath = new \DOMXPath($dom);
        $nodes = $xpath->evaluate("//div[@class=\"dom-body\"]");

        $this->traverseNodes($dom, $nodes[0], $this->id);

        $content = $dom->saveHTML();
        $content = preg_replace('/(\[span)(.*?)(\])(.*?)(\[\/span\])/', '<span $2>$4</span>', $content);

        return $content;

        /**

        $content = mb_convert_encoding($this->content, 'utf-8', mb_detect_encoding($this->content));

        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));

        $xpath = new \DOMXPath($dom);
        $nodes = $xpath->evaluate("//body");

        $this->traverseNodes($dom, $nodes[0], $this->id);

        $content = $dom->saveHTML();
        $content = preg_replace('/(\[span)(.*?)(\])(.*?)(\[\/span\])/', '<span $2>$4</span>', $content);

        return $content;
        **/
    }

    /**
     * @return Question[]
     */
    public function getQuestionsAttribute()
    {
        return isset($this->attributes['questions']) ? $this->attributes['questions'] : [];
    }

    /**
     * @param Question[] $value
     */
    public function setQuestionsAttribute($value)
    {
        $this->attributes['questions'] = $value;
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

    /**
     * @param  \DOMDocument  $dom
     * @param  \DOMNode      $node
     * @param  string        $passage_id
     */
    protected function traverseNodes($dom, $node, $passage_id)
    {
        if ($node->hasChildNodes()) {
            foreach ($node->childNodes as $childNode) {
                $this->traverseNodes($dom, $childNode, $passage_id);
            }
        } else {
            if ($node->nodeType == XML_TEXT_NODE) {
                $text = $node->nodeValue;

                $pattern = '/\'?\w+([-\'’\x{02bb}]\w+)*\'?/u';
                $text    = preg_replace_callback(
                    $pattern,
                    function ($matches) use ($passage_id) {
                        $this->word_count++;

                        return '[span data-passage-id="' . $passage_id . '" data-word-id="' . $this->word_count . '" class="s-text"]' . $matches[0] . '[/span]';
                    },
                    $text
                );

                $node->nodeValue = $text;
            }
        }
    }

}