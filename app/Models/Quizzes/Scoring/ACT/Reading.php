<?php

namespace App\Models\Quizzes\Scoring\ACT;

use App\Models\Model;
use App\Models\Quizzes\Result;
use stdClass as StdClass;

/**
 * Class Reading
 * @package App\Models\Quizzes\Scoring
 *
 * @property int $correct
 * @property int $score
 *
 * @method static Reading find(int $id)
 * @method static Reading findOrFail(int $id)
 *
 * @method static Reading[] get()
 */
class Reading extends Model
{
    const PATH = 'quizzes.scoring.act.reading';
    const SLUG = 'act.reading';

    protected $table = 'quizzes_scorings_act_reading';

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
            ->where('type_id', '=', 'ebba2c77f286f68837fff11176c084a1')
            ->sum('score');

        $score->raw_total = Result\Section::ofResult($result->id)
            ->where('type_id', '=', 'ebba2c77f286f68837fff11176c084a1')
            ->sum('total');

        // Conversion
        $score->scaled  = self::find($score->raw)->score;

        return $score;
    }
}
