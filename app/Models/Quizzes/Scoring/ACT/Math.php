<?php

namespace App\Models\Quizzes\Scoring\ACT;

use App\Models\Model;
use App\Models\Quizzes\Result;
use stdClass as StdClass;

/**
 * Class Math
 * @package App\Models\Quizzes\Scoring
 *
 * @property int $correct
 * @property int $score  Verbal Reasoning
 *
 * @method static Math find(int $id)
 * @method static Math findOrFail(int $id)
 *
 * @method static Math[] get()
 */
class Math extends Model
{
    const PATH = 'quizzes.scoring.act.math';
    const SLUG = 'act.math';

    protected $table = 'quizzes_scorings_act_math';

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
            ->where('type_id', '=', 'c1e50759a61862ae309e068153547de9')
            ->sum('score');
        $score->raw_total = Result\Section::ofResult($result->id)
            ->where('type_id', '=', 'c1e50759a61862ae309e068153547de9')
            ->sum('total');

        // Conversion
        $score->scaled  = self::find($score->raw)->score;

        return $score;
    }
}
