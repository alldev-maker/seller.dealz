<?php

namespace App\Http\Controllers\MediaManager;

use App\Http\Controllers\Controller;
use App\Models\Admin\User;
use App\Models\MediaManager\File;
use App\Models\Model;
use Cocur\Slugify\Slugify;
use Exception;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic;
use stdClass as StdClass;

class IndexController extends Controller
{

    public function index()
    {
        return view('mediamanager.index.index', ['type' => '']);
    }

    public function modal($type = '')
    {
        return view('mediamanager.index.modal', ['type' => $type]);
    }

    public function upload(Request $request)
    {
        try {
            /** @var User $user */
            $user = Auth::user();

            $uploadedFile = $request->file('file');

            $slugify = new Slugify();
            $slug    = $slugify->slugify($uploadedFile->getClientOriginalName());

            $file              = new File();
            $file->id          = $file->generateId();
            $file->slug        = $slug;
            $file->mime_type   = $uploadedFile->getClientMimeType();
            $file->name        = $uploadedFile->getClientOriginalName();
            $file->extension   = $uploadedFile->getClientOriginalExtension();
            $file->description = '';
            $file->owned_by    = $user->id;
            $file->path        = 'users' . DIRECTORY_SEPARATOR . $user->id . DIRECTORY_SEPARATOR;

            $fileNew = $file->id . '.' . $file->extension;

            $dirSrc = $file->path . 'source';
            $dirThm = $file->path . 'thumbnail';
            $dirFrm = $file->path . 'frame';

            $pathSrc = $uploadedFile->storeAs($dirSrc, $fileNew, 'public');

            if ($file->is_video) {
                Storage::makeDirectory('public' . DIRECTORY_SEPARATOR . $dirFrm);
                $base = Storage::disk('public')->getAdapter()->getPathPrefix();

                $ffmpeg = FFMpeg::create([
                    'ffmpeg.binaries'  => env('FFMPEG_BINARIES'),
                    'ffprobe.binaries' => env('FFMPROBE_BINARIES'),
                ]);

                $video    = $ffmpeg->open($base . $pathSrc);
                $frameSrc = $dirFrm . '/' . $file->id . '.jpg';

                $video->frame(TimeCode::fromSeconds(10))->save($base . $frameSrc);
                $contents = Storage::disk('public')->get($frameSrc);
            } else {
                $contents = Storage::disk('public')->get($pathSrc);
            }

            $thumbnail = ImageManagerStatic::make($contents);
            $thumbnail->fit(settings('mm.thumbnail.w'), settings('mm.thumbnail.h'));

            Storage::disk('public')->put($dirThm . DIRECTORY_SEPARATOR . $fileNew, $thumbnail->encode());

            $file->save();

            return $file;
        } catch (Exception $e) {
            abort(500, 'Server Error: ' . $e->getMessage());
        }

    }

    public function list(Request $request, $type = '')
    {
        /** @var User $user */
        $user = Auth::user();

        // Get the default number of items per page
        $dpp = settings('mm.ipp');

        $q = $request->get('q', '');
        $p = (int) $request->get('p', 1);
        $l = (int) $request->get('l', $dpp);
        $f = $request->get('f', []);
        $s = $request->get('s', []);

        $query = with(new File());

        $appends = [];

        if ($l != $dpp) {
            $appends['l'] = $l;
        }


        // ------------------------------------------------------------------
        // Where Clause
        // ------------------------------------------------------------------

        $where         = new StdClass();
        $where->string = '';
        $where->params = [];

        // Query field
        // -----------------------------

        $textSQL = '';

        if ($q != '') {
            $textSQL .= ' (slug LIKE ? OR name LIKE ?) ';

            $where->params[] = "%{$q}%";
            $where->params[] = "%{$q}%";

            $appends['q'] = $q;
        }

        // Filters
        // -----------------------------

        $filterSQL = [];

        if (!empty($type)) {
            switch ($type) {
                case 'video':
                    $filterSQL[] = "`mime_type` IN ('video/mp4', 'video/quicktime')";
                    break;
                case 'image':
                    $filterSQL[] = "`mime_type` IN ('image/jpeg', 'image/png', 'image/gif')";
            }
        }

        if (!in_array($user->role->slug, ['admin', 'developer'])) { // Not admin or developer, filter by user
            $filterSQL[]     = "owned_by = ?";
            $where->params[] = $user->id;
        }

        if (count($f) > 0) {
            foreach ($f as $key => $value) {
                $filterSQL[] = ' ' . $key . ' = ? ';

                $appends['f[' . $key . ']'] = $value;

                $where->params[] = $value;
            }
        }

        $filters = (count($filterSQL) > 0 ? implode(' AND ', $filterSQL) : '1');


        // Combine strings
        // -----------------------------

        $where->string = (!empty($textSQL) ? $textSQL : '1') . ' AND (' . $filters . ')';


        // ------------------------------------------------------------------
        // Sort Clause
        // ------------------------------------------------------------------

        if (empty($s)) {
            $query = $query->orderBy('created_at', 'DESC')->orderBy('name', 'DESC');
        } else {
            foreach ($s as $key => $value) {
                $query                      = $query->orderBy($key, Model::SORT_ORDER[$value]);
                $appends['s[' . $key . ']'] = $value;
            }
        }


        // ------------------------------------------------------------------
        // Pagination
        // ------------------------------------------------------------------

        if ($l > 0) {
            $results = $query->whereRaw($where->string, $where->params)->whereNull('deleted_at')->paginate($l, ['*'], 'p')->appends($appends);
        } else {
            $results = $query->whereRaw($where->string, $where->params)->whereNull('deleted_at')->get();
        }

        unset($p);

        return $l > 0 ? $this->render($results) : $results;
    }

    public function view()
    {

    }

    public function delete($id)
    {
        $file = File::findOrFail($id);

        $fileNew = $file->id . '.' . $file->extension;

        $dirSrc = $file->path . 'source';
        $dirThm = $file->path . 'thumbnail';

        Storage::disk('public')->delete([$dirSrc . DIRECTORY_SEPARATOR . $fileNew, $dirThm . DIRECTORY_SEPARATOR . $fileNew]);

        $file->forceDelete();

        return ['result' => 1];
    }

}
