<?php

namespace App\Models\Quizzes\Scoring\ACT;

use App\Models\Model;
use App\Models\Quizzes\Result;
use stdClass as StdClass;

/**
 * Class Science
 * @package App\Models\Quizzes\Scoring
 *
 * @property int $correct
 * @property int $score  Verbal Reasoning
 *
 * @method static Science find(int $id)
 * @method static Science findOrFail(int $id)
 *
 * @method static Science[] get()
 */
class Science extends Model
{
    const PATH = 'quizzes.scoring.act.science';
    const SLUG = 'act.science';

    protected $table = 'quizzes_scorings_act_science';

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
            ->where('type_id', '=', 'b509a6f4f8f22390abd9615ecfca89ea')
            ->sum('score');
        $score->raw_total = Result\Section::ofResult($result->id)
            ->where('type_id', '=', 'b509a6f4f8f22390abd9615ecfca89ea')
            ->sum('total');

        // Conversion
        $score->scaled  = self::find($score->raw)->score;

        return $score;
    }
}
