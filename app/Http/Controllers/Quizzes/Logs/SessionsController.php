<?php

namespace App\Http\Controllers\Quizzes\Logs;

use App\Http\Controllers\Controller;
use App\Models\Quizzes\Log\Session;
use Illuminate\Http\Request;
use stdClass as StdClass;

class SessionsController extends Controller
{

    public function list(Request $request, $id)
    {
        $eventsRs = Session::ofResult($id)->orderBy('id', 'asc')->get();

        $events = [];

        /** @var Session $event */
        foreach ($eventsRs as $event) {
            $events[] = json_decode($event->content);
        }

        return $events;
    }
}
