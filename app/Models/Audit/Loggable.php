<?php

namespace App\Models\Audit;

use App\Models\Admin\User;
use Illuminate\Support\Facades\Request;

trait Loggable
{

    protected static function boot()
    {
        parent::boot();

        static::created(function ($model) {
            $model->log($model, 'created');
        });

        static::updated(function ($model) {
            $model->log($model, 'updated');
        });

        static::deleted(function ($model) {
            $model->log($model, 'deleted');
        });
    }

    protected function log($model, $event_name) {
        /** @var User $user */
        $user = auth()->user();
        $log  = new Log();

        $log->user_id    = $user->id;
        $log->event      = $event_name;
        $log->model      = get_class($model);
        $log->model_id   = $model->id;
        $log->values_old = json_encode($model->getOriginal());
        $log->values_new = json_encode($model->getAttributes());
        $log->url        = Request::fullUrl();
        $log->ip_address = Request::ip();
        $log->user_agent = Request::header('User-Agent');

        $log->save();
    }

}