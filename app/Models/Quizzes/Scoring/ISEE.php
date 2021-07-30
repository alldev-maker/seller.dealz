<?php

namespace App\Models\Quizzes\Scoring;

use App\Models\Model;
use App\Models\Quizzes\Result;
use stdClass as StdClass;

/**
 * Class ISEE
 * @package App\Models\Quizzes\Scoring
 *
 * @property int $correct
 * @property int $verbal  Verbal Reasoning
 * @property int $qr      Quantitative Reasoning
 * @property int $reading Reading Comprehension
 * @property int $ma      Mathematical Achievement
 *
 * @method static ISEE find(int $id)
 * @method static ISEE findOrFail(int $id)
 *
 * @method static ISEE[] get()
 */
class ISEE extends Model
{
    const PATH = 'quizzes.scoring.isee';
    const SLUG = 'isee';

    protected $table = 'quizzes_scorings_isee';

    protected $primaryKey = 'correct';


    public static function calculate(Result $result)
    {
        $score = new StdClass();

        $score->raw_total    = new StdClass();
        $score->raw_total->verbal = 0;
        $score->raw_total->qr = 0;
        $score->raw_total->reading = 0;
        $score->raw_total->ma = 0;

        $score->raw    = new StdClass();
        $score->raw->verbal = 0;
        $score->raw->qr = 0;
        $score->raw->reading = 0;
        $score->raw->ma = 0;

        $score->scaled = new StdClass();
        $score->scaled->verbal = 0;
        $score->scaled->qr = 0;
        $score->scaled->reading = 0;
        $score->scaled->ma = 0;

        // Verbal Reasoning
        $score->raw->verbal = Result\Section::ofResult($result->id)
            ->where('type_id', '=', 'd30b0f0d84e955ba91d6985bed393800')
            ->sum('score');
        $score->raw_total->verbal = Result\Section::ofResult($result->id)
            ->where('type_id', '=', 'd30b0f0d84e955ba91d6985bed393800')
            ->sum('total');

        // Quantitative Reasoning
        $score->raw->qr = Result\Section::ofResult($result->id)
            ->where('type_id', '=', '12d69b6db3b6a620484a44312e816a7f')
            ->sum('score');
        $score->raw_total->qr = Result\Section::ofResult($result->id)
            ->where('type_id', '=', '12d69b6db3b6a620484a44312e816a7f')
            ->sum('total');

        // Reading Comprehension
        $score->raw->reading = Result\Section::ofResult($result->id)
            ->where('type_id', '=', 'ebba2c77f286f68837fff11176c084a1')
            ->sum('score');
        $score->raw_total->reading = Result\Section::ofResult($result->id)
            ->where('type_id', '=', 'ebba2c77f286f68837fff11176c084a1')
            ->sum('total');

        // Mathematical Ability
        $score->raw->ma = Result\Section::ofResult($result->id)
            ->where('type_id', '=', 'c1e50759a61862ae309e068153547de9')
            ->sum('score');
        $score->raw_total->ma = Result\Section::ofResult($result->id)
            ->where('type_id', '=', 'c1e50759a61862ae309e068153547de9')
            ->sum('total');

        // Conversion
        $score->scaled->verbal  = self::find($score->raw->verbal)->verbal;
        $score->scaled->qr      = self::find($score->raw->qr)->qr;
        $score->scaled->reading = self::find($score->raw->reading)->reading;
        $score->scaled->ma      = self::find($score->raw->ma)->ma;

        // Calculation
        $score->scaled->total   = $score->scaled->verbal + $score->scaled->qr + $score->scaled->reading + $score->scaled->ma;
        $score->scaled->average = round($score->scaled->total / 4);

        return $score;
    }
}
