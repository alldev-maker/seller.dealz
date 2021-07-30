<?php

namespace App\Models\Quizzes\Scoring;

use App\Models\Model;
use App\Models\Quizzes\Result;
use stdClass as StdClass;

/**
 * Class SSAT
 * @package App\Models\Quizzes\Scoring
 *
 * @property int $correct
 * @property int $math_score
 * @property int $math_percent
 * @property int $reading_score
 * @property int $reading_percent
 * @property int $verbal_score
 * @property int $verbal_percent
 *
 * @method static SSAT find(int $id)
 * @method static SSAT findOrFail(int $id)
 *
 * @method static SSAT[] get()
 */
class SSAT extends Model
{
    const PATH = 'quizzes.scoring.ssat';
    const SLUG = 'sat';

    protected $table = 'quizzes_scorings_ssat';

    protected $primaryKey = 'correct';


    public static function calculate(Result $result)
    {
        $score = new StdClass();

        $score->raw       = new StdClass();
        $score->raw_total = new StdClass();
        $score->scaled    = new StdClass();

        // Reading
        $score->raw->reading       = Result\Section::ofResult($result->id)
            ->where('type_id', '=', 'ebba2c77f286f68837fff11176c084a1')
            ->sum('score');
        $score->raw_total->reading = Result\Section::ofResult($result->id)
            ->where('type_id', '=', 'ebba2c77f286f68837fff11176c084a1')
            ->sum('total');

        // Verbal
        $score->raw->verbal       = Result\Section::ofResult($result->id)
            ->where('type_id', '=', 'd30b0f0d84e955ba91d6985bed393800')
            ->sum('score');
        $score->raw_total->verbal = Result\Section::ofResult($result->id)
            ->where('type_id', '=', 'd30b0f0d84e955ba91d6985bed393800')
            ->sum('total');

        // Math
        $score->raw->math       = Result\Section::ofResult($result->id)
            ->where('type_id', '=', 'c1e50759a61862ae309e068153547de9')
            ->sum('score');
        $score->raw_total->math = Result\Section::ofResult($result->id)
            ->where('type_id', '=', 'c1e50759a61862ae309e068153547de9')
            ->sum('total');

        // Conversion
        $score->scaled->reading_num = self::find($score->raw->reading)->reading_score;
        $score->scaled->reading_per = self::find($score->raw->reading)->reading_percent;
        $score->scaled->verbal_num  = self::find($score->raw->verbal)->verbal_score;
        $score->scaled->verbal_per  = self::find($score->raw->verbal)->verbal_percent;
        $score->scaled->math_num    = self::find($score->raw->math)->math_score;
        $score->scaled->math_per    = self::find($score->raw->math)->math_percent;

        // Calculation
        $score->scaled->total_num = $score->scaled->reading_num + $score->scaled->verbal_num + $score->scaled->math_num;
        $score->scaled->total_per = $score->scaled->reading_per + $score->scaled->verbal_per + $score->scaled->math_per;

        $score->scaled->average_num = round($score->scaled->total_num / 3);
        $score->scaled->average_per = round($score->scaled->total_per / 3);

        return $score;
    }
}
