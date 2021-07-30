<?php

namespace App\Models\Quizzes\Types;

use App\Models\Model;

/**
 * Class Scoring
 *
 * @package App\Models\Admin
 *
 * @property int $id
 * @property string $slug
 * @property string $name
 *
 * @method static Scoring find(int $id)
 * @method static Scoring findOrFail(int $id)
 *
 * @method static Scoring[] get()
 */
class Scoring extends Model
{
    const PATH = 'quizzes.types.scorings';
    const SLUG = 'scoring';

    protected $table = 'quizzes_scorings';
}
