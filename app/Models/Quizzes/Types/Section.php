<?php

namespace App\Models\Quizzes\Types;

use App\Models\Model;

/**
 * Class Section
 * @package App\Models\Quizzes
 *
 * @property string $id
 * @property string $name
 * @property string $slug
 * @property string $description
 */
class Section extends Model
{
    const PATH = 'quizzes.types.sections';
    const SLUG = 'type';

    protected $table = 'quizzes_types_sections';

    protected $fillable = [
        'name',
        'description',
    ];

    protected $appends = ['urls'];

    public function getUrlsAttribute()
    {
        $urls = [];

        if (!empty($this->id)) {
            $urls['edit'] = route(sprintf('%s.edit', self::PATH), ['id' => $this->id]);
        }

        return $urls;
    }
}
