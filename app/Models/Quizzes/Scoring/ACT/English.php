<?php

namespace App\Models\Quizzes\Scoring\ACT;

use App\Models\Model;
use App\Models\Quizzes\Result;
use stdClass as StdClass;

/**
 * Class English
 * @package App\Models\Quizzes\Scoring
 *
 * @property int $correct
 * @property int $score  Verbal Reasoning
 *
 * @method static English find(int $id)
 * @method static English findOrFail(int $id)
 *
 * @method static English[] get()
 */
class English extends Model
{
    const PATH = 'quizzes.scoring.act.english';
    const SLUG = 'act.english';

    protected $table = 'quizzes_scorings_act_english';

    protected $primaryKey = 'correct';


    public static function calculate(Result $result)
    {
        $score = new StdClass();

        $score->raw    = new StdClass();
        $score->raw = 0;

        $score->raw_total   = new StdClass();
        $score->raw_total = 0;

        $score->scaled = new StdClass();
        $score->scaled = 0;

        $score->raw = Result\Section::ofResult($result->id)
            ->where('type_id', '=', 'f16748fb36d4ce112668c68bd45e02b5')
            ->sum('score');
        $score->raw_total = Result\Section::ofResult($result->id)
            ->where('type_id', '=', 'f16748fb36d4ce112668c68bd45e02b5')
            ->sum('total');

        // Conversion
        $score->scaled  = self::find($score->raw)->score;

        return $score;
    }
}
