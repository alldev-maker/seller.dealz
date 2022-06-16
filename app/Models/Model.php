<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;

/**
 * Class Model
 *
 * @package App\Models
 *
 * @property array  $urls
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $deleted_at
 *
 * @method static bool create(array $params)
 * @method static Model|null find(string $id)
 * @method static Model findOrFail(string $id)
 * @method static Model|null first()
 * @method static Collection|Builder where($column, $operator = null, $value = null, $boolean = 'and')
 * @method static Collection|Builder whereRaw(string $query, array $params)
 * @method static Collection|Builder whereIn(string $field, array $values)
 * @method static LengthAwarePaginator paginate(int $perPage, $columns = ['*'], string $pageName = 'page')
 * @method static int count()
 *
 * @method void makeHidden($attributes)
 */
class Model extends EloquentModel
{
    use SoftDeletes;

    const SORT_ORDER = [
        'a' => 'ASC',
        'd' => 'DESC',
    ];

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DELETED_AT = 'deleted_at';

    public $incrementing = false;

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public static function getTableName()
    {
        return with(new static)->getTable();
    }

    public function generateId()
    {
        $rand = md5(uniqid(rand()));
        $rand = substr($rand, 0, 49);
        return ($rand);
    }

    public function generateUd() {
        return uniqid('_');
    }

}