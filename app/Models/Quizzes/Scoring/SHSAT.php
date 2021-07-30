<?php

namespace App\Models\Quizzes\Scoring;

use App\Models\Model;
use App\Models\Quizzes\Result;
use stdClass as StdClass;

/**
 * Class SHSAT
 * @package App\Models\Quizzes\Scoring
 *
 * @property int $correct
 * @property int $score
 *
 * @method static SHSAT find(int $id)
 * @method static SHSAT findOrFail(int $id)
 *
 * @method static SHSAT[] get()
 */
class SHSAT extends Model
{
    const PATH = 'quizzes.scoring.shsat';
    const SLUG = 'shsat';

    protected $table = 'quizzes_scorings_shsat';

    protected $primaryKey = 'correct';


    public static function calculate(Result $result)
    {
        $score = new StdClass();

        $score->raw    = new StdClass();
        $score->raw->overall = 0;

        $score->scaled = new StdClass();
        $score->scaled->overall = 0;

        // Overall
        $score->raw->overall = Result\Section::ofResult($result->id)->sum('score');

        // Conversion
        $score->scaled->overall  = self::find($score->raw->overall)->score;

        // Calculation
        $score->scaled->total   =  $score->scaled->overall;

        return $score;
    }
}
