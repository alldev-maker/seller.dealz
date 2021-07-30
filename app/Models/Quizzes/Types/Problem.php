<?php

namespace App\Models\Quizzes\Types;

use App\Models\Model;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;


/**
 * Class Problem
 * @package App\Models\Quizzes
 *
 * @property string $key         Unique identifier.
 * @property string $name        Name of the Problem Type.
 * @property string $value       Variable used for <code>voerro/vue-tagsinuput</code> Vue component.
 * @property string $description Descrption of the Problem Type.
 * @property int    $visible     Sets the visibility of the tag in the Result
 *
 * @method static Problem find(string $key)
 * @method static Problem findOrFail(int $id)
 * @method static Problem firstOrCreate(array $attributes, array $values)
 * @method static Problem firstOrNew(array $attributes, array $values)
 * @method static Problem join(string $table, string $left, string $operator, string $right)
 * @method static Problem leftJoin(string $table, string $left, string $operator, string $right)
 * @method static Problem rightJoin(string $table, string $left, string $operator, string $right)
 *
 * @method static Problem[] get()
 *
 * @method static Problem|QueryBuilder ofVisibility(string $id)
 */
class Problem extends Model
{
    const PATH = 'quizzes.types.problems';
    const SLUG = 'type';

    protected $table      = 'quizzes_types_problems';
    protected $primaryKey = 'key';


    protected $fillable = [
        'name',
        'description',
        'visible',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $appends = [
        'value',
        'urls',
    ];

    /**
     * Scope a query to specify visibility of the Problem Type.
     *
     * @param  QueryBuilder  $query
     * @param  int           $visibility
     *
     * @return QueryBuilder
     */
    public function scopeOfVisibility($query, $visibility)
    {
        return $query->where('visibility', '=', $visibility);
    }

    public function getValueAttribute()
    {
        return $this->name;
    }

    public function getUrlsAttribute()
    {
        $urls = [];

        if (!empty($this->key)) {
            $urls['edit'] = route(sprintf('%s.edit', self::PATH), ['id' => $this->key]);
        }

        return $urls;
    }
}
