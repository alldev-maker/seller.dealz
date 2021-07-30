<?php

namespace App\Models\Quizzes\Scoring;

use App\Models\Model;
use App\Models\Quizzes\Result;
use stdClass as StdClass;

/**
 * Class SAT
 * @package App\Models\Quizzes\Scoring
 *
 * @property int $correct
 * @property int $reading
 * @property int $writing
 * @property int $math
 *
 * @method static SAT find(int $id)
 * @method static SAT findOrFail(int $id)
 *
 * @method static SAT[] get()
 */
class SAT extends Model
{
    const PATH = 'quizzes.scoring.sat';
    const SLUG = 'sat';

    protected $table = 'quizzes_scorings_sat';

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

        // Writing and language
        $score->raw->writing       = Result\Section::ofResult($result->id)
            ->where('type_id', '=', 'af7676dc73f1542eea4ffb95ccfc0817')
            ->sum('score');
        $score->raw_total->writing = Result\Section::ofResult($result->id)
            ->where('type_id', '=', 'af7676dc73f1542eea4ffb95ccfc0817')
            ->sum('total');

        // Math
        $score->raw->math       = Result\Section::ofResult($result->id)
            ->where('type_id', '=', 'c1e50759a61862ae309e068153547de9')
            ->sum('score');
        $score->raw_total->math = Result\Section::ofResult($result->id)
            ->where('type_id', '=', 'c1e50759a61862ae309e068153547de9')
            ->sum('total');

        // Conversion
        $score->scaled->reading = self::find($score->raw->reading)->reading;
        $score->scaled->writing = self::find($score->raw->writing)->writing;
        $score->scaled->math    = self::find($score->raw->math)->math;

        // Calculation
        $score->raw->english       = $score->raw->reading + $score->raw->writing;
        $score->raw_total->english = $score->raw_total->reading + $score->raw_total->writing;
        $score->scaled->english    = ($score->scaled->reading + $score->scaled->writing) * 10;
        $score->scaled->total      = $score->scaled->english + $score->scaled->math;

        return $score;
    }
}
