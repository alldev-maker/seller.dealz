<?php

namespace App\Models\Quizzes;

use App\Models\Audit\Loggable;
use App\Models\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;


/**
 * Class Session
 *
 * @package App\Models\Quiz
 *
 * @property string $id
 * @property string $quiz_id
 * @property Carbon $alive_until
 * @property Quiz   $quiz
 *
 * @method static Session find(string $id)
 * @method static Session findOrFail(string $id)
 *
 * @method static Session[] get()
 *
 * @method static QueryBuilder|Session ofQuiz(string $id)
 */
class Session extends Model
{
    const PATH = 'quizzes.quizzes.sessions';
    const SLUG = 'session';

    protected $table = 'quizzes_sessions';

    protected $casts = [
        'alive_until' => 'datetime',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
        'deleted_at'  => 'datetime',
    ];

    protected $hidden = [
        'quiz_id',
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
     * Usage: $session->quiz
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
            'sessions',
            'urls',
        ];

        $quiz->makeHidden($hideFields);

        return $quiz;
    }

    /**
     * An accessor method to return the URLs of the Section.
     *
     * Usage: $session->urls
     *
     * @return array
     */
    public function getUrlsAttribute()
    {
        return [];
    }

}