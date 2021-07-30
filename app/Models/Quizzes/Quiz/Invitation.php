<?php

namespace App\Models\Quizzes\Quiz;

use App\Models\Audit\Loggable;
use App\Models\Quizzes\Quiz;
use App\Models\Model;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Notifications\Notifiable;


/**
 * Class Invitation
 *
 * @package App\Models\Quiz
 *
 * @property string    $id
 * @property string    $quiz_id
 * @property string    $email
 * @property string    $user_id
 * @property string    $testtaker_id
 * @property int       $is_submitted
 *
 * @property Quiz      $quiz
 *
 * @method static Invitation find(int $id)
 * @method static Invitation findOrFail(int $id)
 *
 * @method static Invitation[] get()
 *
 * @method static QueryBuilder|Invitation ofQuiz(string $id)
 * @method static QueryBuilder|Invitation ofEmail(string $email)
 * @method static QueryBuilder|Invitation ofUser(string $id)
 * @method static QueryBuilder|Invitation ofTesttaker(string $id)
 */
class Invitation extends Model
{
    const PATH = 'quizzes.quizzes.invitations';
    const SLUG = 'invitation';

    protected $table = 'quizzes_quizzes_invitations';

    protected $casts = [
        'is_submitted' => 'boolean',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
        'deleted_at'   => 'datetime',
    ];

    protected $fillable = [
        'email',
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
        'urls',
    ];

    /**
     * Scope a query to specify questions of a given quiz ID.
     *
     * @param  QueryBuilder  $query
     * @param  string        $quiz_id
     *
     * @return QueryBuilder
     */
    public function scopeOfQuiz($query, $quiz_id)
    {
        return $query->where('quiz_id', '=', $quiz_id);
    }

    /**
     * @param  QueryBuilder  $query
     * @param  string        $email
     *
     * @return QueryBuilder
     */
    public function scopeOfEmail($query, $email)
    {
        return $query->where('email', '=', $email);
    }

    /**
     * @param  QueryBuilder  $query
     * @param  string        $user_id
     *
     * @return QueryBuilder
     */
    public function scopeOfUser($query, $user_id)
    {
        return $query->where('user_id', '=', $user_id);
    }

    /**
     * @param  QueryBuilder  $query
     * @param  string        $testtaker_id
     *
     * @return QueryBuilder
     */
    public function scopeOfTesttaker($query, $testtaker_id)
    {
        return $query->where('testtaker_id', '=', $testtaker_id);
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
     * An accessor method to return the URLs of the Invitation.
     *
     * Usage: $section->urls
     *
     * @return array
     */
    public function getUrlsAttribute()
    {
        return [];
    }

}