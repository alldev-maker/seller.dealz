<?php

namespace App\Models\Quizzes\Session;

use App\Models\Log;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

/**
 * Class WebSession
 *
 * @package App\Models\Session
 *
 * @property int    $id
 * @property string $session_id
 * @property string $content
 * @property int    $ordering
 *
 * @method static WebSession find(int $id)
 * @method static WebSession findOrFail(int $id)
 *
 * @method static WebSession[] get()
 *
 * @method static QueryBuilder ofSession(string $id)
 */
class WebSession extends Log
{
    protected $table = 'quizzes_sessions_websessions';

    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * Scope a query to specify sections of a given session ID.
     *
     * @param  QueryBuilder  $query
     * @param  string        $session_id
     *
     * @return QueryBuilder
     */
    public function scopeOfSession($query, $session_id)
    {
        return $query->where('session_id', '=', $session_id);
    }
}