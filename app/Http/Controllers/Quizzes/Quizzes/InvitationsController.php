<?php

namespace App\Http\Controllers\Quizzes\Quizzes;

use App\Http\Controllers\Controller;
use App\Models\Admin\User;
use App\Models\Model;
use App\Models\Quizzes\Quiz;
use App\Models\Quizzes\Quiz\Invitation;
use App\Models\Roster\Testtaker;
use App\Models\Settings\Setting;
use App\Notifications\InviteUsersForQuiz;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use stdClass as StdClass;

class InvitationsController extends Controller
{

    /**
     * InvitationsController constructor.
     */
    public function __construct()
    {
        $this->name = [
            'singular' => 'Invitation',
            'plural'   => 'Invitations',
        ];
    }

    /**
     * @param  Request  $request
     * @param  string   $id  Invitation ID
     *
     * @return JsonResponse
     */
    public function list(Request $request, string $id = '')
    {
        /** @var Setting $defaultPerPage */
        $defaultPerPage = Setting::where('key', 'site.ipp.tabular')->first();

        $q = $request->get('q', '');
        $p = (int) $request->get('p', 1);
        $l = (int) $request->get('l', $defaultPerPage->value);
        $f = $request->get('f', []);
        $s = $request->get('s', []);

        $f['qid'] = !empty($id) ? $id : '';

        $query = with(new Invitation());

        $appends = [];

        if ($l != $defaultPerPage->value) {
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
            $textSQL .= ' email LIKE ? ';

            $where->params[] = "%{$q}%";

            $appends['q'] = $q;
        }

        // Filters
        // -----------------------------

        $filterSQL = '';
        if (count($f) > 0) {
            foreach ($f as $key => $value) {
                if ($key == 'qid') {
                    $filterSQL .= ' quiz_id = ? ';
                } else {
                    $filterSQL                  .= ' ' . $key . ' = ? ';
                    $appends['f[' . $key . ']'] = $value;
                }

                $where->params[] = $value;
            }
        }

        // Combine strings
        // -----------------------------

        $where->string = (!empty($textSQL) ? $textSQL : '1') . ' AND ' . (!empty($filterSQL) ? $filterSQL : '1');


        // ------------------------------------------------------------------
        // Sort Clause
        // ------------------------------------------------------------------

        if (empty($s)) {
            $query = $query->orderBy('created_at', 'ASC')->orderBy('email', 'ASC');
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
            $invitations = $query->whereRaw($where->string, $where->params)->whereNull('deleted_at')->paginate($l, ['*'], 'p')->appends($appends);
        } else {
            $invitations = $query->whereRaw($where->string, $where->params)->whereNull('deleted_at')->get();
        }

        unset($p);

        return $l > 0 ? $this->render($invitations) : $invitations;
    }

    /**
     * Get the Invitation.
     *
     * @param $id
     * @param $sid
     *
     * @return Invitation
     */
    public function show($id, $sid)
    {
        unset($id);

        return Invitation::findOrFail($sid);
    }

    public function create(Request $request, $id)
    {
        $quizId = $id;
        $fields = $request->all();

        $message    = new StdClass();
        $invitation = new Invitation();

        DB::beginTransaction();

        try {
            $quiz      = Quiz::find($quizId);
            $addresses = explode("\n", $fields['addresses']);

            foreach ($addresses as $address) {
                $role = Setting::find('testtaker.role');
                $user = User::ofRole($role->value)->where('email', '=', trim($address))->first();

                $invitation = new Invitation();

                $invitation->id      = $invitation->generateId();
                $invitation->quiz_id = $quiz->id;
                $invitation->email   = $address;

                if ($user instanceof User) {
                    $testtaker = Testtaker::where('email', '=', trim($address))->first();

                    $invitation->user_id      = $user->id;
                    $invitation->testtaker_id = $testtaker->id;

                    $invitation->save();

                    $testtaker->quizzes()->attach($quiz->id);

                    $user->notify(new InviteUsersForQuiz($quiz, $user));
                } else {
                    $invitation->save();

                    Notification::route('mail', $address)->notify(new InviteUsersForQuiz($quiz));
                }
            }

            DB::commit();

            $message->status  = 'success';
            $message->content = $this->name['singular'] . ' has been created.';

        } catch (\Exception $e) {
            DB::rollback();

            $message->status  = 'danger';
            $message->content = 'Failed to create a ' . $this->name['singular'] . '.';
            $message->reason  = $e->getMessage();
        }

        if ($message->status === 'success') {
            return response()->json($invitation, 201);
        } else {
            return response()->json($message, 500);
        }
    }

    public function update(Request $request, $id, $sid)
    {
        $quizId = $id;
        $fields = $request->all();

        $message    = new StdClass();
        $invitation = new Invitation();


        DB::beginTransaction();

        try {
            $invitation = Invitation::findOrFail($sid);

            $invitation->quiz_id    = $quizId;
            $invitation->type_id    = $fields['type']['id'] ?? '';
            $invitation->time_limit = $fields['time_limit'] ?? 0;

            $invitation->save();

            DB::commit();

            $message->status  = 'success';
            $message->content = $this->name['singular'] . ' has been updated.';

        } catch (\Exception $e) {
            DB::rollback();

            $message->status  = 'danger';
            $message->content = 'Failed to create a ' . $this->name['singular'] . '.';
            $message->reason  = $e->getMessage();
        }

        if ($message->status === 'success') {
            return response()->json($invitation, 200);
        } else {
            return response()->json($message, 500);
        }

    }

    public function updateMultiple(Request $request)
    {
        $action = $request->get('action');

        switch ($action) {
            case 'sort':
                return $this->sort($request);
            default:
                return response()->json(null);
        }
    }

    public function delete(Request $request, $id, $sid)
    {
        unset($request);
        unset($id);

        $message = new StdClass();

        /** @var Invitation $entity */
        $invitation = Invitation::findOrFail($sid);

        DB::beginTransaction();

        try {
            Invitation::destroy($invitation->id);

            $message->status = 'success';

            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();

            $message->status  = 'danger';
            $message->content = 'Failed to delete a ' . $this->name['singular'] . '.';
            $message->reason  = $e->getMessage();
        }


        if ($message->status === 'success') {
            return response()->json(['result' => 1]);
        } else {
            return response()->json($message, 500);
        }

    }

    public function deleteMultiple(Request $request)
    {
        $ids = $request->get('ids');

        Invitation::whereIn('id', $ids)->delete();

        return response()->json(['result' => 1]);
    }

}
