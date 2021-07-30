<?php

namespace App\Models\Quizzes;

use App\Models\Admin\User;
use App\Models\Audit\Loggable;
use App\Models\Model;
use App\Models\Quizzes\Quiz\Section;
use App\Models\Quizzes\Types\Scoring;
use App\Models\Roster\Testtaker;
use Carbon\Carbon;


/**
 * Class Quiz
 *
 * @package App\Models\Quizzes
 *
 * @property string      $id
 * @property string      $name
 * @property string      $description
 * @property int         $scoring_type_id
 * @property string      $user_id
 * @property boolean     $enabled
 * @property Carbon      $enabled_until
 * @property int         $duration
 * @property boolean     $auto_submit
 * @property boolean     $shuffle_passages
 * @property boolean     $shuffle_questions
 * @property boolean     $shuffle_choices
 * @property boolean     $allow_guests
 * @property boolean     $multiple_takes
 * @property int         $taken_count
 * @property string      $content_upload
 * @property string      $content_after
 *
 * @property string|null $page
 *
 * @property User        $created_by
 * @property Section[]   $sections
 *
 * @method static Quiz find(int $id)
 * @method static Quiz findOrFail(int $id)
 */
class Quiz extends Model
{
    use Loggable;

    const PATH = 'quizzes.quizzes';
    const SLUG = 'quiz';

    protected $table = 'quizzes_quizzes';

    protected $casts = [
        'enabled'           => 'boolean',
        'enabled_until'     => 'datetime',
        'auto_submit'       => 'boolean',
        'shuffle_passages'  => 'boolean',
        'shuffle_questions' => 'boolean',
        'shuffle_choices'   => 'boolean',
        'allow_guests'      => 'boolean',
        'multiple_takes'    => 'boolean',

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
        'user',
        'scoring_type',
        'page',
        'urls',
    ];

    public function testtakers()
    {
        return $this->belongsToMany(Testtaker::class, 'roster_testtakers_quizzes')
            ->withPivot('takes')
            ->withTimestamps();
    }

    /**
     * An accessor method to get the User attribute created the quiz.
     *
     * Usage: $quiz->user
     *
     * @return User
     */
    public function getUserAttribute()
    {
        $user = User::find($this->user_id);

        if (!$user) {
            $user = new User();

            $user->name = '';
        }

        $user->makeHidden(['role_id', 'role', 'notes', 'urls', 'device_id', 'app_id', 'app_token']);

        return $user;
    }

    public function getScoringTypeAttribute()
    {
        $type = Scoring::find($this->scoring_type_id);

        if (!$type) {
            $type = new Scoring();

            $type->name = '';
        }

        return $type;
    }

    /**
     * An accessor method to return the URL of the Quiz in the frontend.
     *
     * Usage: $quiz->page
     *
     * @return null|string
     */
    public function getPageAttribute()
    {
        if ($this->enabled) {
            return route('qz.form', ['id' => $this->id]);
        } else {
            return null;
        }
    }

    /**
     * An accessor method to return the URLs of the Quiz.
     *
     * Usage: $quiz->urls
     *
     * @return array
     */
    public function getUrlsAttribute()
    {
        $urls = [];

        $urls['edit']          = route(sprintf('%s.edit', self::PATH), ['id' => $this->id]);
        $urls['questionnaire'] = route(sprintf('%s.questionnaire', self::PATH), ['id' => $this->id]);
        $urls['sections']      = route(sprintf('%s.sections.index', self::PATH), ['id' => $this->id]);
        $urls['settings']      = route(sprintf('%s.settings', self::PATH), ['id' => $this->id]);
        $urls['view']          = route(sprintf('%s.view', self::PATH), ['id' => $this->id]);
        $urls['frontend']      = route('qz.form', ['id' => $this->id]);

        return $urls;
    }

}