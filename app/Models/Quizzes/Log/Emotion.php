<?php

namespace App\Models\Quizzes\Log;

use App\Models\Log;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use stdClass as StdClass;

/**
 * Class Emotion
 *
 * @package App\Models\Quizzes\Log
 *
 * @property int    $id
 * @property string $result_id
 * @property int    $emotion
 * @property int    $probability
 * @property int    $clock
 *
 * @method static Emotion find(int $id)
 * @method static Emotion findOrFail(int $id)
 *
 * @method static QueryBuilder|Emotion ofResult(string $id)
 * @method static QueryBuilder|Emotion ofEmotion(int $code)
 */
class Emotion extends Log
{
    const PINCHED_EYEBROWS = 0;
    const PINCHED_NOSE     = 1;
    const WIDE_EYES        = 2;
    const SMILE            = 3;
    const FROWN            = 4;
    const RAISED_EYEBROWS  = 5;
    const RELAXED          = 6;

    protected $table = 'quizzes_logs_emotions_sec';

    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * Scope a query to specify rows of a given Result ID.
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
     * Scope a query to specify rows of a given emotion code.
     *
     * @param  QueryBuilder $query
     * @param  int       $code
     *
     * @return QueryBuilder
     */
    public function scopeOfEmotion($query, $code)
    {
        return $query->where('emotion', '=', $code);
    }

    /**
     * @param $value
     *
     * @return float
     */
    public function getProbabilityAttribute($value)
    {
        return round($value * 100);
    }

    public function getEmotionAttribute($value) {
        $emotion = new StdClass();
        $emotion->id = $value;

        switch ($value) {
            case self::PINCHED_EYEBROWS:
                $emotion->name = 'Pinched Eyebrows';
                break;
            case self::PINCHED_NOSE:
                $emotion->name = 'Pinched Nose';
                break;
            case self::WIDE_EYES:
                $emotion->name = 'Wide Eyes';
                break;
            case self::SMILE:
                $emotion->name = 'Smile';
                break;
            case self::FROWN:
                $emotion->name = 'Frown';
                break;
            case self::RAISED_EYEBROWS:
                $emotion->name = 'Raised Eyebrows';
                break;
            case self::RELAXED:
                $emotion->name = 'Relaxed';
                break;
        }

        return $emotion;
    }
}