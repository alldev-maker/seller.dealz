<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use stdClass as StdClass;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $name;

    protected $viewPath;

    public function render(LengthAwarePaginator $model)
    {
        $output          = new StdClass();
        $output->records = $model->items();

        $items = new StdClass();

        $items->count = count($model->items());
        $items->total = $model->total();

        $pages          = new StdClass();
        $pages->current = $model->currentPage();
        $pages->total   = $model->lastPage();
        $pages->per     = $model->perPage();

        $links           = new StdClass();
        $links->first    = $model->url(1);
        $links->previous = $model->previousPageUrl();
        $links->next     = $model->nextPageUrl();
        $links->last     = $model->url($model->lastPage());

        $pagination          = new StdClass();
        $pagination->records = $items;
        $pagination->pages   = $pages;
        $pagination->links   = $links;

        $output->pagination = $pagination;

        return response()->json($output);
    }
}
