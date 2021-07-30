<?php

namespace App\Http\Controllers\Geography;

use App\Models\Model;
use stdClass as StdClass;
use App\Http\Controllers\Controller;
use App\Models\Geography\Country;
use App\Models\Settings\Setting;
use Cocur\Slugify\Slugify;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class CountriesController extends Controller
{

    public function __construct()
    {
        $this->name = [
            'singular' => 'Country',
            'plural'   => 'Countries',
        ];
    }

    public function index(Request $request)
    {
        $message = $request->session()->get('message');
        $request->session()->forget('message');

        return view(sprintf('%s.index', Country::PATH), ['message' => $message]);
    }

    public function form($id = '')
    {
        $country = $id == '' ? new Country() : Country::findOrFail($id);

        if ($id == '') {
            $country->id            = '';
            $country->slug          = '';
            $country->name_common   = '';
            $country->name_official = '';
        }

        return view(sprintf('%s.form', Country::PATH), ['country' => $country]);
    }

    public function list(Request $request)
    {
        /** @var Setting $defaultPerPage */
        $defaultPerPage = Setting::where('key', 'site.ipp.tabular')->first();

        $q = $request->get('q', '');
        $p = (int) $request->get('p', 1);
        $l = (int) $request->get('l', $defaultPerPage->value);
        $f = $request->get('f', []);
        $s = $request->get('s', []);

        $appends = [];
        if ($l != $defaultPerPage->value) {
            $appends['l'] = $l;
        }

        $where         = new StdClass();
        $where->string = '';
        $where->params = [];

        $textSQL = '';
        if ($q != '') {
            $textSQL .= ' slug LIKE ? OR name_common LIKE ? OR name_official LIKE ?';

            $where->params[] = "%{$q}%";
            $where->params[] = "%{$q}%";
            $where->params[] = "%{$q}%";
        }

        $filterSQL = '';

        $where->string = (!empty($textSQL) ? $textSQL : '1') . ' AND ' . (!empty($filterSQL) ? $filterSQL : '1');

        $query = with(new Country);

        if (empty($s)) {
            $query = $query->orderBy('id', 'ASC');
        } else {
            foreach ($s as $key => $value) {
                $query = $query->orderBy($key, Model::SORT_ORDER[$value]);
            }
        }

        $appends = [];
        if ($q != '') {
            $appends['q'] = $q;
        }
        if (count($s) > 0) {
            foreach ($s as $key => $value) {
                $query                      = $query->orderBy($key, Model::SORT_ORDER[$value]);
                $appends['s[' . $key . ']'] = $value;
            }
        }

        if ($l > 0) {
            $country = $query->whereRaw($where->string, $where->params)->whereNull('deleted_at')->paginate($l, ['*'], 'p')->appends($appends);
        } else {
            $country = $query->whereRaw($where->string, $where->params)->whereNull('deleted_at')->get();
            $country->makeHidden(['description', 'urls', 'prefix']);
        }

        unset($p);

        return $l > 0 ? $this->render($country) : $country;
    }

    public function show($id)
    {
        return Country::findOrFail($id);
    }

    public function create(Request $request)
    {
        $validation = Validator::make(
            $request->all(), ['name' => 'required']
        );

        if ($validation->fails()) {
            $errors = $validation->errors();

            return response()->json($errors->toJson(), 400);
        } else {
            $slugify = new Slugify();

            $name_common = $request->get('name_common');
            $slug        = $request->get('slug');
            $slug        = empty($slug) ? $slugify->slugify($name_common) : $slug;

            $entity = new Country();

            $entity->slug          = $slug;
            $entity->name_common   = $name_common;
            $entity->name_official = $request->get('name_official');

            $entity->save();

            $message          = new StdClass();
            $message->status  = 'success';
            $message->content = $this->name['singular'] . ' has been created.';

            $request->session()->put('message', $message);

            return response()->json($entity, 201);
        }
    }

    public function update(Request $request, $id)
    {
        $validation = Validator::make(
            $request->all(), ['name' => 'required']
        );

        if ($validation->fails()) {
            $errors = $validation->errors();

            return response()->json($errors->toJson(), 400);
        } else {
            $slugify = new Slugify();

            $name_common = $request->get('name_common');
            $slug        = $request->get('slug');
            $slug        = empty($slug) ? $slugify->slugify($name_common) : $slug;

            /** @var Country $entity */
            $entity = Country::findOrFail($id);

            $entity->slug        = $slug;
            $entity->name        = $name;
            $entity->description = $request->get('description');
            $entity->iso_code    = $request->get('iso_code');
            $entity->prefix      = $request->get('prefix');

            $entity->save();

            $message          = new StdClass();
            $message->status  = 'success';
            $message->content = $this->name['singular'] . ' has been updated.';

            $request->session()->put('message', $message);

            return response()->json($entity);
        }
    }

    public function updateMultiple(Request $request)
    {

    }

    public function delete(Request $request, $id)
    {
        unset($request);

        /** @var Country $entity */
        $entity = Country::findOrFail($id);

        Country::destroy($entity->id);

        return response()->json(['result' => 1]);
    }

    public function deleteMultiple(Request $request)
    {
        $ids = $request->get('ids');

        Country::whereIn('id', $ids)->delete();

        return response()->json(['result' => 1]);
    }

    protected function listGeneral()
    {

    }

    protected function listComplete()
    {

    }
}
