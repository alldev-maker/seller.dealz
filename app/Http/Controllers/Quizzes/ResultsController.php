<?php

namespace App\Http\Controllers\Quizzes;

use App\Http\Controllers\Controller;
use App\Models\Model;
use App\Models\Quizzes\Log;
use App\Models\Quizzes\Quiz;
use App\Models\Quizzes\Result;
use App\Models\Quizzes\Scoring\SAT as ScoringSAT;
use App\Models\Quizzes\Scoring\ISEE as ScoringISEE;
use App\Models\Quizzes\Scoring\SSAT as ScoringSSAT;
use App\Models\Quizzes\Scoring\SHSAT as ScoringSHAT;
use App\Models\Quizzes\Scoring\ACT as ScoringACT;
use App\Models\Quizzes\Types\Scoring;
use App\Models\Settings\Setting;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Khill\Duration\Duration;
use stdClass as StdClass;

class ResultsController extends Controller
{

    /**
     * ResultsController constructor.
     */
    public function __construct()
    {
        $this->name = [
            'singular' => 'Result',
            'plural'   => 'Results',
        ];
    }

    public function index(Request $request)
    {
        $message = $request->session()->get('message');
        $request->session()->forget('message');

        return view(sprintf('%s.index', Result::PATH), ['message' => $message]);
    }

    public function summary($id)
    {
        $result = Result::find($id);
        $log    = Log::find($id);

        return view(sprintf('%s.summary', Result::PATH), ['result' => $result, 'log' => $log]);
    }

    public function tracking($id)
    {
        $result = Result::find($id);

        return view(sprintf('%s.tracking', Result::PATH), ['result' => $result]);
    }

    public function session($id)
    {
        $result = Result::find($id);

        return view(sprintf('%s.session', Result::PATH), ['result' => $result]);
    }

    public function chart($id)
    {
        $result = Result::find($id);

        return view(sprintf('%s.chart', Result::PATH), ['result' => $result]);
    }

    public function video($id)
    {
        $result = Result::find($id);

        return view(sprintf('%s.video', Result::PATH), ['result' => $result]);
    }

    public function eyetracking($id)
    {
        $result = Result::find($id);

        $sections = Result\Section::where('result_id', '=', $result->id)->whereNull('deleted_at')->orderBy('ordering', 'ASC')->get();

        foreach ($sections as $section) {

            $passages = Result\Passage::ofSection($section->id)->whereNull('deleted_at')->orderBy('ordering', 'ASC')->get();

            foreach ($passages as $passage) {
                $questions = Result\Question::ofPassage($passage->id)->whereNull('deleted_at')->orderBy('ordering', 'ASC')->get();

                $i = 1;
                foreach ($questions as $question) {
                    $question->number = $i++;
                }

                $passage->questions = $questions;
            }

            $section->passages = $passages;
        }

        $result->sections = $sections;

        return view(sprintf('%s.eyetracking', Result::PATH), ['result' => $result]);
    }

    public function answerkey($id)
    {
        $result = Result::find($id);

        $sections = Result\Section::where('result_id', '=', $result->id)->whereNull('deleted_at')->orderBy('ordering', 'ASC')->get();

        foreach ($sections as $section) {

            // Filling in Questions per Section
            // -----------------------------

            $questions = Result\Question::ofSection($section->id)->whereNull('deleted_at')->orderBy('ordering', 'ASC')->get();

            $i = 1;
            foreach ($questions as $question) {
                $question->number = $i++;
            }

            $section->questions = $questions;

            $maxcols = settings('results.strip.length');
            $total   = count($section->questions);
            $group   = ceil($total / $maxcols);


            // Answers Strip Blocking
            // -----------------------------

            $questions = $section->questions->all();
            $blocks    = [];
            $currentQ  = current($questions);

            for ($g = 1; $g <= $group; $g++) {
                $start      = 1;
                $blocks[$g] = [];
                while ($start <= $maxcols && $currentQ) {
                    $currentQ = current($questions);
                    if ($currentQ) {
                        $blocks[$g][] = $currentQ;
                    }
                    $start++;
                    next($questions);
                }
            }

            $section->blocks = $blocks;
        }

        $result->sections = $sections;

        return view(sprintf('%s.answerkey', Result::PATH), ['result' => $result]);
    }

    public function timing($id)
    {
        $result   = Result::find($id);
        $duration = new Duration();

        $sections = Result\Section::where('result_id', '=', $result->id)->whereNull('deleted_at')->orderBy('ordering', 'ASC')->get();

        foreach ($sections as $section) {

            $passages = Result\Passage::ofSection($section->id)->whereNull('deleted_at')->orderBy('ordering', 'ASC')->get();

            foreach ($passages as $passage) {
                $total_time = Log\Slide::ofResult($result->id)
                    ->ofType('passage')
                    ->ofReference($passage->passage_id)
                    ->where('duration', '>', 0) // TODO: Include this on settings as Reading Treshold. How many seconds of duration to be considered as reading.
                    ->sum('duration');
                $read_count = Log\Slide::ofResult($result->id)
                    ->ofType('passage')
                    ->ofReference($passage->passage_id)
                    ->where('duration', '>', 0) // TODO: Include this on settings as Reading Treshold. How many seconds of duration to be considered as reading.
                    ->count('id');

                $average = $read_count > 0 ? (round($total_time / $read_count)) : null;

                $passage->read_time = new StdClass();

                $seconds                          = $total_time / 1000;
                $passage->read_time->milliseconds = $total_time;
                $passage->read_time->seconds      = $total_time / 1000;
                $passage->read_time->human        = $duration->humanize(round($seconds));

                $passage->read_count = $read_count;

                $passage->read_avg = new StdClass();

                $passage->read_avg->seconds = $average;
                $passage->read_avg->human   = !is_null($average) ? $duration->humanize(round($average / 1000)) : null;

                $questions = Result\Question::ofPassage($passage->id)->whereNull('deleted_at')->orderBy('ordering', 'ASC')->get();

                foreach ($questions as $question) {
                    $total_time = Log\Slide::ofResult($result->id)
                        ->ofType('question')
                        ->ofReference($question->question_id)
                        ->where('duration', '>', 0) // TODO: Include this on settings as Reading Treshold. How many seconds of duration to be considered as reading.
                        ->sum('duration');
                    $read_count = Log\Slide::ofResult($result->id)
                        ->ofType('question')
                        ->ofReference($question->question_id)
                        ->where('duration', '>', 0) // TODO: Include this on settings as Reading Treshold. How many seconds of duration to be considered as reading.
                        ->count('id');

                    $average = $read_count > 0 ? (($total_time / $read_count) / 1000) : null;

                    $question->read_time = new StdClass();

                    $seconds                           = $total_time / 1000;
                    $question->read_time->milliseconds = $total_time;
                    $question->read_time->seconds      = $total_time / 1000;
                    $question->read_time->human        = $duration->humanize(round($seconds));

                    $question->read_count = $read_count;

                    $question->read_avg = new StdClass();

                    $question->read_avg->seconds = $average;
                    $question->read_avg->human   = !is_null($average) ? $duration->humanize(round($average)) : null;
                }

                $passage->questions = $questions;
            }

            $section->passages = $passages;
        }

        $result->sections = $sections;

        return view(sprintf('%s.timing', Result::PATH), ['result' => $result]);
    }

    public function scores($id)
    {
        $result = Result::find($id);
        $type   = Scoring::find($result->scoring_type_id);
        $scores = Result\Score::ofResult($id)->get();

        return view(sprintf('%s.scores', Result::PATH), ['result' => $result, 'type' => $type, 'scores' => $scores]);
    }

    public function download($id)
    {
        $result = Result::find($id);
        $log    = Log::find($id);

        $params = [
            'result' => $result,
            'log'    => $log,
        ];

        $pdf = SnappyPdf::loadView('pdf.results', $params);
        $pdf->setPaper('a4');

        //return view('pdf.results', $params);
        return $pdf->inline($id . '.pdf');
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

        $query = with(new Result());

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
            $textSQL .= ' (test_taker_name LIKE ? OR quiz_name LIKE ?) ';

            $where->params[] = "%{$q}%";
            $where->params[] = "%{$q}%";

            $appends['q'] = $q;
        }

        // Filters
        // -----------------------------

        $filterSQL = '';
        if (count($f) > 0) {
            foreach ($f as $key => $value) {
                switch ($key) {
                    case 'q':
                        $filterSQL .= ' `quiz_id` = ? ';
                        break;
                    default:
                        $filterSQL .= ' `' . $key . '` = ? ';
                }

                $appends['f[' . $key . ']'] = $value;

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
            $query = $query->orderBy('created_at', 'DESC')->orderBy('quiz_name', 'ASC')->orderBy('test_taker_name', 'ASC');
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

    public function show($id)
    {
        return Result::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $fields = $request->all();

        $action = isset($fields['action']) ? $fields['action'] : '';

        switch ($action) {
            case 'ppt':
                return $this->setProblemTypes($id);
            case 'rcl':
                return $this->recalculate($id);
            default:
                return true;
        }
    }

    public function delete(Request $request, $id)
    {
        unset($request);

        $message = new StdClass();

        DB::beginTransaction();

        try {
            $this->deleteSingle($id);

            $message->status = 'success';

            DB::commit();
        } catch (Exception $e) {
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

        $message = new StdClass();

        DB::beginTransaction();

        try {
            foreach ($ids as $id) {
                $this->deleteSingle($id);
            }
            $message->status = 'success';

            DB::commit();
        } catch (Exception $e) {
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

    protected function deleteSingle($id)
    {
        // -- Delete Results --

        $result = Result::find($id);

        if ($result) {
            Result\Section::ofResult($id)->forceDelete();
            Result\Passage::ofResult($id)->forceDelete();

            $questions = Result\Question::ofResult($id)->get();

            foreach ($questions as $question) {
                Result\Choice::ofQuestion($question->id)->forceDelete();
                Result\Answer::ofQuestion($question->id)->forceDelete();
                Result\ProblemType::ofQuestion($question->id)->forceDelete();

                $question->forceDelete();
            }

            $result->forceDelete();
        }

        // -- Delete Logs --

        $log = Log::find($id);

        if ($log) {
            Log\Answer::ofResult($id)->forceDelete();
            Log\Blink::ofResult($id)->forceDelete();
            Log\Dilation::ofResult($id)->forceDelete();
            Log\Emotion::ofResult($id)->forceDelete();
            Log\Pulse::ofResult($id)->forceDelete();
            Log\Session::ofResult($id)->forceDelete();
            Log\Slide::ofResult($id)->forceDelete();
            Log\Slouch::ofResult($id)->forceDelete();
            Log\Word::ofResult($id)->forceDelete();

            $log->forceDelete();
        }

        $path = 'results' . DIRECTORY_SEPARATOR . 'videos' . DIRECTORY_SEPARATOR . $id;
        Storage::disk('public')->delete($path . '.webm');
        Storage::disk('public')->delete($path . '.mp4');
    }

    protected function setProblemTypes($id)
    {
        Result\ProblemType::ofResult($id)->forceDelete();

        $sections = Result\Section::ofResult($id)->get();
        foreach ($sections as $section) {
            $passages = Result\Passage::ofSection($section->id)->get();
            foreach ($passages as $passage) {
                $questions = Result\Question::ofPassage($passage->id)->get();
                foreach ($questions as $question) {
                    $probtypes = Quiz\ProblemType::ofQuestion($question->question_id)->get();

                    /** @var Quiz\ProblemType $probtype */
                    foreach ($probtypes as $probtype) {
                        $probtypeR = new Result\ProblemType();

                        $probtypeR->id          = $probtypeR->generateId();
                        $probtypeR->result_id   = $id;
                        $probtypeR->section_id  = $section->id;
                        $probtypeR->passage_id  = $passage->id;
                        $probtypeR->question_id = $question->id;
                        $probtypeR->key         = $probtype->key;

                        $probtypeR->save();
                    }
                }
            }
        }

        return ['result' => 1];
    }

    protected function recalculate($id)
    {
        $result                     = Result::find($id);
        $result->total              = 0;
        $result->score              = 0;
        $result->questions_count    = 0;
        $result->questions_answered = 0;
        $result->questions_skipped  = 0;
        $result->answers_correct    = 0;
        $result->answers_incorrect  = 0;

        $sections = Result\Section::ofResult($result->id)->get();

        /** @var Result\Section $section */
        foreach ($sections as $section) {
            $section->total              = 0;
            $section->score              = 0;
            $section->questions_total    = 0;
            $section->questions_answered = 0;
            $section->questions_skipped  = 0;
            $section->answers_correct    = 0;
            $section->answers_incorrect  = 0;

            $passages = Result\Passage::ofSection($section->id)->get();

            /** @var Result\Passage $passage */
            foreach ($passages as $passage) {
                $questions = Result\Question::ofPassage($passage->id)->get();

                /** @var Result\Question $question */
                foreach ($questions as $question) {
                    $result->questions_count++;

                    $corrects = Result\Choice::correct($question->id)->get();
                    $answer   = Result\Answer::ofQuestion($question->id)->first();

                    $section->total += $question->points;
                    $result->total  += $question->points;

                    if ($answer->choice_id === '') {
                        $section->questions_skipped++;
                        $result->questions_skipped++;
                        continue;
                    }

                    $section->questions_answered++;
                    $result->questions_answered++;

                    /** @var Result\Choice $correct */
                    foreach ($corrects as $correct) {
                        if ($answer->choice_id === $correct->choice_id) {
                            $question->score = $question->points;

                            $section->score += $question->points;
                            $result->score  += $question->points;

                            $section->answers_correct++;
                            $result->answers_correct++;
                            break;
                        }

                        $section->answers_incorrect++;
                        $result->answers_incorrect++;
                    }

                    $question->save();
                }
            }

            $section->save();
        }

        // Scoring System

        $scoring = Scoring::find($result->scoring_type_id);
        Result\Score::ofResult($result->id)->forceDelete();

        switch ($scoring->slug) {
            case 'sat':
                $score = ScoringSAT::calculate($result);

                $ssr_obj               = new Result\Score();
                $ssr_obj->name         = 'English Language';
                $ssr_obj->result_id    = $result->id;
                $ssr_obj->score_raw    = $score->raw->english;
                $ssr_obj->score_raw_total    = $score->raw_total->english;
                $ssr_obj->score_scaled = $score->scaled->english;
                $ssr_obj->save();

                $ssm_obj               = new Result\Score();
                $ssm_obj->name         = 'Mathematics';
                $ssm_obj->result_id    = $result->id;
                $ssm_obj->score_raw    = $score->raw->math;
                $ssm_obj->score_scaled = $score->scaled->math;
                $ssm_obj->save();

                $result->score_scaled = $score->scaled->total;

                break;
            case 'isee':
                $score = ScoringISEE::calculate($result);

                $ss                  = new Result\Score();
                $ss->name            = 'Verbal Reasoning';
                $ss->result_id       = $result->id;
                $ss->score_raw       = $score->raw->verbal;
                $ss->score_raw_total = $score->raw_total->verbal;
                $ss->score_scaled    = $score->scaled->verbal;
                $ss->save();

                $ss                  = new Result\Score();
                $ss->name            = 'Quantitative Reasoning';
                $ss->result_id       = $result->id;
                $ss->score_raw       = $score->raw->qr;
                $ss->score_raw_total = $score->raw_total->qr;
                $ss->score_scaled    = $score->scaled->qr;
                $ss->save();

                $ss                  = new Result\Score();
                $ss->name            = 'Reading Comprehension';
                $ss->result_id       = $result->id;
                $ss->score_raw       = $score->raw->reading;
                $ss->score_raw_total = $score->raw_total->reading;
                $ss->score_scaled    = $score->scaled->reading;
                $ss->save();

                $ss                  = new Result\Score();
                $ss->name            = 'Mathematical Achievement';
                $ss->result_id       = $result->id;
                $ss->score_raw       = $score->raw->ma;
                $ss->score_raw_total = $score->raw_total->ma;
                $ss->score_scaled    = $score->scaled->ma;
                $ss->save();

                $result->score_scaled = $score->scaled->average;

                break;
            case 'ssat':
                $score = ScoringSSAT::calculate($result);

                $ssr_obj                  = new Result\Score();
                $ssr_obj->name            = 'Reading';
                $ssr_obj->result_id       = $result->id;
                $ssr_obj->score_raw       = $score->raw->reading;
                $ssr_obj->score_raw_total = $score->raw_total->reading;
                $ssr_obj->score_scaled    = $score->scaled->reading_num;
                $ssr_obj->score_percent   = $score->scaled->reading_per;
                $ssr_obj->save();

                $ssw_obj                  = new Result\Score();
                $ssw_obj->name            = 'Verbal';
                $ssw_obj->result_id       = $result->id;
                $ssw_obj->score_raw       = $score->raw->verbal;
                $ssw_obj->score_raw_total = $score->raw_total->verbal;
                $ssw_obj->score_scaled    = $score->scaled->verbal_num;
                $ssw_obj->score_percent   = $score->scaled->verbal_per;
                $ssw_obj->save();

                $ssm_obj                  = new Result\Score();
                $ssm_obj->name            = 'Mathematics';
                $ssm_obj->result_id       = $result->id;
                $ssm_obj->score_raw       = $score->raw->math;
                $ssm_obj->score_raw_total = $score->raw_total->math;
                $ssm_obj->score_scaled    = $score->scaled->math_num;
                $ssm_obj->score_percent   = $score->scaled->math_per;
                $ssm_obj->save();

                $result->score_scaled = $score->scaled->total_num;

                break;
            case 'shsat':
                $score = ScoringSHAT::calculate($result);

                $ss               = new Result\Score();
                $ss->name         = 'Overall';
                $ss->result_id    = $result->id;
                $ss->score_raw    = $score->raw->overall;
                $ss->score_scaled = $score->scaled->overall;
                $ss->save();

                $result->score_scaled = $score->scaled->overall;

                break;
            case 'act':
                $score_eng = ScoringACT\English::calculate($result);
                $s         = new Result\Score();

                $s->name            = 'English';
                $s->result_id       = $result->id;
                $s->score_raw       = $score_eng->raw;
                $s->score_raw_total = $score_eng->raw_total;
                $s->score_scaled    = $score_eng->scaled;
                $s->save();

                $score_mth = ScoringACT\Math::calculate($result);
                $s         = new Result\Score();

                $s->name            = 'Mathematics';
                $s->result_id       = $result->id;
                $s->score_raw       = $score_mth->raw;
                $s->score_raw_total = $score_mth->raw_total;
                $s->score_scaled    = $score_mth->scaled;
                $s->save();

                $score_rdn = ScoringACT\Reading::calculate($result);
                $s         = new Result\Score();

                $s->name            = 'Reading';
                $s->result_id       = $result->id;
                $s->score_raw       = $score_rdn->raw;
                $s->score_raw_total = $score_rdn->raw_total;
                $s->score_scaled    = $score_rdn->scaled;
                $s->save();

                $score_sci = ScoringACT\Science::calculate($result);
                $s         = new Result\Score();

                $s->name            = 'Science';
                $s->result_id       = $result->id;
                $s->score_raw       = $score_sci->raw;
                $s->score_raw_total = $score_sci->raw_total;
                $s->score_scaled    = $score_sci->scaled;
                $s->save();

                break;
        }

        $result->save();

        return $result;
    }
}
