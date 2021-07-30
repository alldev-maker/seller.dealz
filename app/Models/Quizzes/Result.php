<?php

namespace App\Models\Quizzes;

use App\Models\Admin\User;
use Carbon\Carbon;
use App\Models\Model;
use Khill\Duration\Duration;

/**
 * Class Result
 *
 * @package App\Models\Quizzes
 *
 * @property string           $id
 * @property string           $quiz_id             Quiz ID.
 * @property string           $quiz_name           Name of the Quiz.
 * @property string           $quiz_description    The description of the quiz.
 * @property int              $scoring_type_id
 * @property string           $session_id
 * @property string           $family_name         Test taker's family name.
 * @property string           $given_name          Test taker's given name.
 * @property string           $test_taker_name     Test taker's full name.
 * @property string           $email               Test taker's email address.
 * @property Carbon           $time_start
 * @property Carbon           $time_end
 * @property int              $duration            Duration of the Quiz, in seconds.
 * @property int              $total               Total points.
 * @property int              $score               Points earned.
 * @property int              $score_scaled        Score to display after conversion.
 * @property int              $questions_count     Number of questions.
 * @property int              $questions_answered  Questions answered, regarless if correct or not.
 * @property int              $questions_skipped   Questions skipped.
 * @property int              $answers_correct     Correct answers.
 * @property int              $answers_incorrect   Incorrect answers.
 * @property int              $calibration         Calibration score.
 * @property int              $avg_pulse           Average pulse.
 * @property int              $target              Target score.
 *
 * @property User             $created_by
 * @property Result\Section[] $sections
 *
 * @method static Result find(string $id)
 * @method static Result findOrFail(string $id)
 *
 * @method static Result[] get()
 */
class Result extends Model
{

    const PATH = 'quizzes.results';
    const SLUG = 'result';

    protected $table = 'quizzes_results';

    protected $casts = [
        'time_start' => 'datetime',
        'time_end'   => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $hidden = [
        'user_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $appends = [
        'duration',
        'urls',
    ];

    /**
     * An accessor method to return the duration of the test.
     *
     * Usage: $quiz->duration
     *
     * @return string
     */
    public function getDurationAttribute()
    {
        $interval = $this->time_end->diff($this->time_start);
        $duration = new Duration($interval->format('%h:%i:%s'));

        return $duration->humanize();
    }

    /**
     * An accessor method to return the URLs of the Result.
     *
     * Usage: $quiz->urls
     *
     * @return array
     */
    public function getUrlsAttribute()
    {
        return [
            'summary' => route(sprintf('%s.summary', self::PATH), ['id' => $this->id]),
            'session' => route(sprintf('%s.session', self::PATH), ['id' => $this->id]),
            'chart'   => route(sprintf('%s.chart', self::PATH), ['id' => $this->id]),
            'video'   => route(sprintf('%s.video', self::PATH), ['id' => $this->id]),
            'strip'   => route(sprintf('%s.answerkey', self::PATH), ['id' => $this->id]),
        ];
    }

}