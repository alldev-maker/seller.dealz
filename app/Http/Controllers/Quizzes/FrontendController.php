<?php

namespace App\Http\Controllers\Quizzes;

use App\Http\Controllers\Controller;
use App\Models\Quizzes\Log;
use App\Models\Quizzes\Log\Session;
use App\Models\Quizzes\Quiz;
use App\Models\Quizzes\Result;
use App\Models\Quizzes\Scoring\ISEE as ScoringISEE;
use App\Models\Quizzes\Scoring\SAT as ScoringSAT;
use App\Models\Quizzes\Scoring\SHSAT as ScoringSHAT;
use App\Models\Quizzes\Scoring\SSAT as ScoringSSAT;
use App\Models\Quizzes\Scoring\ACT as ScoringACT;
use App\Models\Quizzes\Session as QuizSession;
use App\Models\Quizzes\Types\Scoring;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass as StdClass;

class FrontendController extends Controller
{

    /**
     * FrontendController constructor.
     */
    public function __construct()
    {
        $this->name = [
            'singular' => 'Quiz',
            'plural'   => 'Quizzes',
        ];
    }

    public function index()
    {
        return view('quizzes.frontend.index');
    }

    public function form($id = '', $name = null)
    {
        $quiz    = Quiz::findOrFail($id);
        $session = new QuizSession();

        if (!$quiz->enabled) {
            abort(404);
        }

        // ------------------------------------------------------------------
        // Generate Quiz Session. This will be used for streaming videos
        // and rrweb sessions.
        // ------------------------------------------------------------------

        $session->id          = random_string();
        $session->quiz_id     = $quiz->id;
        $session->alive_until = (new Carbon())->addHours(6);

        $session->save();

        // ------------------------------------------------------------------
        // Duplicate the quiz. Which contains the IDs.
        // ------------------------------------------------------------------

        $testpaper      = new StdClass();
        $sections_array = [];

        $testpaper->id       = $quiz->id;
        $testpaper->sections = [];

        $sections = Quiz\Section::ofQuiz($quiz->id)->whereNull('deleted_at')->orderBy('ordering', 'ASC')->orderBy('name', 'ASC')->get();

        /** @var Quiz\Section $section */
        foreach ($sections as $section) {
            $sectionObj           = new StdClass();
            $sectionObj->id       = $section->id;
            $sectionObj->passages = [];

            $sections_array[$section->id] = [
                'time_limit' => $section->time_limit,
                'allow_skip' => false,
            ];

            $passages = Quiz\Passage::ofSection($section->id)->whereNull('deleted_at')->orderBy('ordering', 'ASC')->orderBy('name', 'ASC')->get();


            if ($quiz->shuffle_passages) {
                $passages = $passages->shuffle();
            }

            foreach ($passages as $passage) {
                $passageObj            = new StdClass();
                $passageObj->id        = $passage->id;
                $passageObj->questions = [];

                $questions = Quiz\Question::ofPassage($passage->id)->whereNull('deleted_at')->orderBy('ordering', 'ASC')->orderBy('question', 'ASC')->get();

                if ($quiz->shuffle_questions) {
                    $questions = $questions->shuffle();
                }

                foreach ($questions as $question) {
                    $questionObj          = new StdClass();
                    $questionObj->id      = $question->id;
                    $questionObj->choices = [];

                    $choices = $question->choices;

                    switch ($question->shuffle_choices) {
                        case 1: // No
                            break;
                        case 2: // Yes
                            shuffle($choices);
                            break;
                        case 0: // Inherit from Quiz Settings
                        default:
                            if ($quiz->shuffle_choices) {
                                shuffle($choices);
                            }
                    }

                    $letter = 'A';
                    foreach ($choices as $choice) {
                        $choice->letter = $letter;

                        $choiceObj         = new StdClass();
                        $choiceObj->id     = $choice->id;
                        $choiceObj->letter = $letter;

                        $questionObj->choices[] = $choiceObj;

                        $letter++;
                    }

                    $question->choices_computed = $choices;

                    $passageObj->questions[] = $questionObj;
                }

                $passage->questions     = $questions;
                $sectionObj->passages[] = $passageObj;
            }

            $section->passages     = $passages;
            $testpaper->sections[] = $sectionObj;
        }

        $quiz->sections = $sections;

        $view_name = 'quizzes.frontend.form' . ($name != null ? '-' . $name : '');

        return view($view_name, [
            'quiz'           => $quiz,
            'session'        => $session,
            'testpaper'      => $testpaper,
            'sections_array' => $sections_array,
        ]);
    }

    public function get_sections($id = '', $sid = '')
    {
        $sid  = QuizSession::findOrFail($sid);
        $quiz = Quiz::findOrFail($id);

        if (!$quiz->enabled) {
            abort(404);
        }

        $sections = Quiz\Section::ofQuiz($quiz->id)->whereNull('deleted_at')->orderBy('ordering', 'ASC')->orderBy('name', 'ASC')->get();

        foreach ($sections as $section) {
            $section->makeHidden(['urls']);

            $passages = Quiz\Passage::ofSection($section->id)->whereNull('deleted_at')->orderBy('ordering', 'ASC')->orderBy('name', 'ASC')->get();

            if ($quiz->shuffle_passages) {
                $passages = $passages->shuffle();
            }

            foreach ($passages as $passage) {
                $passage->makeHidden(['urls', 'section']);

                $questions = Quiz\Question::ofPassage($passage->id)->whereNull('deleted_at')->orderBy('ordering', 'ASC')->orderBy('question', 'ASC')->get();

                if ($quiz->shuffle_questions) {
                    $questions = $questions->shuffle();
                }

                foreach ($questions as $question) {
                    $question->makeHidden(['section', 'passage', 'choices', 'urls']);
                    $choices = $question->choices;

                    switch ($question->shuffle_choices) {
                        case 1: // No
                            break;
                        case 2: // Yes
                            shuffle($choices);
                            break;
                        case 0: // Inherit from Quiz Settings
                        default:
                            if ($quiz->shuffle_choices) {
                                shuffle($choices);
                            }
                    }

                    $letter = 'A';
                    foreach ($choices as $choice) {
                        $choice->letter = $letter;
                        $letter++;
                    }

                    $question->choices_computed = $choices;
                }
                $passage->questions = $questions;
            }

            $section->passages = $passages;
        }

        return $sections;
    }

    public function post_answer(Request $request, $id)
    {

    }

    public function upload_videos(Request $request, $id)
    {
        $fields = $request->all();

        $session_id = $fields['sid'];
        $video_file = $request->file('video');

        $dir_src  = 'sessions' . DIRECTORY_SEPARATOR . $session_id . DIRECTORY_SEPARATOR;
        $file_new = $video_file->getClientOriginalName();

        $video_file->storeAs($dir_src, $file_new, 'public');
    }

    public function upload_websessions(Request $request, $id)
    {
        $current_date = new Carbon();

        $fields = $request->all();

        $session_id = $fields['sid'];
        $websession = isset($fields['rrweb']) ? $fields['rrweb'] : [];

        if (count($websession) < 1) {
            return ['result' => 1];
        }

        $ordering = 0;
        foreach ($websession as $websess) {
            $ordering            = isset($websess['ordering']) ? $websess['ordering'] : $ordering++;
            $session             = new QuizSession\WebSession();
            $session->session_id = $session_id;
            $session->content    = $websess['content'];
            $session->ordering   = $ordering;
            $session->created_at = $current_date;

            $session->save();
        }

        return ['result' => 1];
    }

    public function create(Request $request, $id)
    {
        $fields = $request->all();

        $session_id = $fields['sid'];
        $data       = isset($fields['data']) ? json_decode($fields['data'], true) : [];

        if (empty($data)) {
            abort(400, 'Data is required');
        }

        $testpaper = isset($data['testpaper']) ? $data['testpaper'] : [];

        if (empty($testpaper)) {
            abort(400, 'Testpaper data is required.');
        }

        $quiz = Quiz::findOrFail($id);

        try {
            DB::beginTransaction();

            $curdate = new Carbon();

            // ------------------------------------------------------------------
            // Save the questions and answers.
            // ------------------------------------------------------------------

            $result = new Result();

            $result->id               = $result->generateId();
            $result->quiz_id          = $quiz->id;
            $result->quiz_name        = $quiz->name;
            $result->quiz_description = $quiz->description;
            $result->session_id       = $session_id;
            $result->test_taker_name  = $data['name'];
            $result->email            = $data['email'];
            $result->time_start       = $data['time']['start'];
            $result->time_end         = $data['time']['end'];
            $result->target           = $data['target_score'];
            $result->scoring_type_id  = $quiz->scoring_type_id;

            $result->calibration = $data['logs']['calibration'];

            foreach ($testpaper['sections'] as $k => $s) {
                $sectionQ = Quiz\Section::find($s['id']);
                $sectionR = new Result\Section();

                $sectionR->id          = $sectionR->generateId();
                $sectionR->result_id   = $result->id;
                $sectionR->section_id  = $sectionQ->id;
                $sectionR->type_id     = $sectionQ->type_id;
                $sectionR->name        = $sectionQ->name;
                $sectionR->description = $sectionQ->description;
                $sectionR->time_limit  = $sectionQ->time_limit;
                $sectionR->ordering    = ++$k;

                $sectionR->save();

                foreach ($s['passages'] as $l => $p) {
                    $passageQ = Quiz\Passage::find($p['id']);
                    $passageR = new Result\Passage();

                    $passageR->id           = $passageR->generateId();
                    $passageR->result_id    = $result->id;
                    $passageR->section_id   = $sectionR->id;
                    $passageR->passage_id   = $passageQ->id;
                    $passageR->name         = $passageQ->name;
                    $passageR->description  = $passageQ->description;
                    $passageR->content      = $passageQ->content;
                    $passageR->content_html = $data['html']['passages'][$p['id']] ?? $passageQ->content;
                    $passageR->ordering     = ++$l;

                    $passageR->save();

                    foreach ($p['questions'] as $m => $q) {
                        $questionQ = Quiz\Question::find($q['id']);
                        $questionR = new Result\Question();

                        $questionR->id            = $questionR->generateId();
                        $questionR->result_id     = $result->id;
                        $questionR->section_id    = $sectionR->id;
                        $questionR->passage_id    = $passageR->id;
                        $questionR->question_id   = $questionQ->id;
                        $questionR->question      = $questionQ->question;
                        $questionR->question_html = $data['html']['questions'][$q['id']] ?? $questionQ->question;
                        $questionR->points        = $questionQ->points;
                        $questionR->type          = $questionQ->type;
                        $questionR->difficulty    = $questionQ->difficulty;
                        $questionR->explain_video = $questionQ->explain_video;
                        $questionR->ordering      = ++$m;

                        $questionR->save();

                        $probtypes = Quiz\ProblemType::ofQuestion($questionQ->id)->get();

                        /** @var Quiz\ProblemType $probtype */
                        foreach ($probtypes as $probtype) {
                            $probtypeR = new Result\ProblemType();

                            $probtypeR->id          = $probtypeR->generateId();
                            $probtypeR->result_id   = $result->id;
                            $probtypeR->section_id  = $sectionR->id;
                            $probtypeR->passage_id  = $passageR->id;
                            $probtypeR->question_id = $questionR->id;
                            $probtypeR->key         = $probtype->key;

                            $probtypeR->save();
                        }

                        $result->questions_count++;
                        $result->total += $questionR->points;

                        $letter = 'A';
                        foreach ($q['choices'] as $n => $c) {
                            $choiceQ = Quiz\Choice::find($c['id']);
                            $choiceR = new Result\Choice();

                            $choiceR->id          = $choiceR->generateId();
                            $choiceR->result_id   = $result->id;
                            $choiceR->section_id  = $sectionR->id;
                            $choiceR->passage_id  = $passageR->id;
                            $choiceR->question_id = $questionR->id;
                            $choiceR->choice_id   = $choiceQ->id;
                            $choiceR->choice_name = $choiceQ->choice;
                            $choiceR->choice_html = $data['html']['choices'][$c['id']] ?? $choiceQ->choice;
                            $choiceR->letter      = $letter++;
                            $choiceR->is_correct  = $choiceQ->is_correct;
                            $choiceR->points      = $choiceQ->points;
                            $choiceR->ordering    = ++$n;

                            $choiceR->save();
                        }

                        $answer = new Result\Answer();

                        $answer->id          = $answer->generateId();
                        $answer->question_id = $questionR->id;

                        if (!empty($data['answers'][$q['id']])) { // Get the answer
                            $choiceA = Result\Choice::where('result_id', $result->id)->where('choice_id', $data['answers'][$q['id']])->first();

                            $answer->choice_id   = $choiceA->choice_id;
                            $answer->choice_name = $choiceA->choice_name;
                            $answer->letter      = $choiceA->letter;
                        } else { // Question is skipped
                            $answer->choice_id   = '';
                            $answer->choice_name = '';
                            $answer->letter      = '';
                        }

                        $answer->save();
                    }

                }
            }

            // ------------------------------------------------------------------
            // Moving uploaded file to proper storage.
            // ------------------------------------------------------------------

            //$videoFile = $request->file('video');

            //$dirSrc  = 'results' . DIRECTORY_SEPARATOR . 'videos' . DIRECTORY_SEPARATOR;
            //$fileNew = $result->id . '.' . $videoFile->getClientOriginalExtension();

            //$videoFile->storeAs($dirSrc, $fileNew, 'public');

            // ------------------------------------------------------------------
            // Create Log data
            // ------------------------------------------------------------------

            $log = new Log();

            $log->id = $result->id;

            $log->save();

            // ------------------------------------------------------------------
            // Log remaining web sessions
            // ------------------------------------------------------------------

            $websession = isset($data['logs']['rrweb']) ? $data['logs']['rrweb'] : [];

            foreach ($websession as $k => $websess) {
                $wsobj = new QuizSession\WebSession();

                $wsobj->session_id = $session_id;
                $wsobj->content    = $websess['content'];
                $wsobj->ordering   = $websess['ordering'];
                $wsobj->created_at = $curdate;

                $wsobj->save();
            }

            // Copy session to log

            $websessions = QuizSession\WebSession::ofSession($session_id)
                ->orderBy('ordering', 'asc')
                ->get();
            foreach ($websessions as $websess) {
                $websesslog = new Session();

                $websesslog->result_id  = $result->id;
                $websesslog->content    = $websess->content;
                $websesslog->created_at = $curdate;

                $websesslog->save();
            }

            // ------------------------------------------------------------------
            // Log words.
            // ------------------------------------------------------------------

            $words = isset($data['logs']['words']) ? $data['logs']['words'] : [];

            if (!empty($words)) {
                foreach ($words as $word) {
                    $row = new Log\Word();

                    $row->result_id   = $result->id;
                    $row->passage_id  = $word['passageId'];
                    $row->question_id = $word['questionId'];
                    $row->choice_id   = $word['choiceId'];
                    $row->word_id     = $word['wordId'];
                    $row->word_text   = $word['wordText'];
                    $row->created_at  = $curdate;

                    $row->save();
                }
            }

            // ------------------------------------------------------------------
            // Log slides.
            // ------------------------------------------------------------------

            $slides = isset($data['logs']['slides']) ? $data['logs']['slides'] : [];

            if (empty($slides)) {
                abort(400, 'Slides log is required.');
            }

            foreach ($slides as $slide) {
                $row = new Log\Slide();

                $row->result_id      = $result->id;
                $row->type           = $slide['type'];
                $row->reference_id   = $slide['id'];
                $row->answered       = $slide['answered'];
                $row->passage_reread = $slide['passage_reread'];
                $row->clock          = $slide['clock'];
                $row->duration       = $slide['duration'];
                $row->created_at     = $curdate;

                $row->save();
            }

            // ------------------------------------------------------------------
            // Log answers.
            // ------------------------------------------------------------------

            $answers = isset($data['logs']['answers']) ? $data['logs']['answers'] : [];

            if (empty($answers)) {
                abort(400, 'Answers log is required.');
            }

            foreach ($answers as $answer) {
                $row = new Log\Answer();

                $row->result_id   = $result->id;
                $row->question_id = $answer['questionId'];
                $row->choice_id   = $answer['choiceId'];

                /** @var Result\Question $question */
                $choice = Result\Choice::where('result_id', '=', $result->id)->where('choice_id', '=', $answer['choiceId'])->first();

                $row->letter      = $choice->letter;
                $row->choice_name = $choice->choice_name;
                $row->clock       = $answer['clock'];

                $row->save();
            }

            // ------------------------------------------------------------------
            // Log read count.
            // ------------------------------------------------------------------

            $this->count_reads($result);
            $this->count_passage_reads($result);

            // ------------------------------------------------------------------
            // Calculate the scores.
            // ------------------------------------------------------------------

            $this->calculate_scores($result);

            // ------------------------------------------------------------------
            // Run script for pulse rate, pupil dilation, emotion, et al.
            // ------------------------------------------------------------------

            $analysisScript = base_path() . '/scripts/analyze-video.sh';
            $command        = 'nohup ' . $analysisScript . ' ' . $session_id . ' ' . $result->id . ' > /dev/null 2>&1 &';

            system($command);

            DB::commit();

            return [
                'result' => 1,
            ];
        } catch (Exception $e) {
            DB::rollBack();

            return abort(500, $e->getMessage());
        }
    }

    /**
     * Count number of reads in a Passage and in a Question.
     *
     * @param  Result  $result
     */
    protected function count_reads(Result $result)
    {
        $sections = Result\Section::ofResult($result->id)->get();

        foreach ($sections as $section) {
            $passages = Result\Passage::ofSection($section->id)->get();

            foreach ($passages as $passage) {
                $count = Log\Slide::ofResult($result->id)
                    ->ofType('passage')
                    ->where('reference_id', '=', $passage->passage_id)
                    ->count('id');

                $passage->read_count = $count;
                $passage->save();

                $questions = Result\Question::ofPassage($passage->id)->get();

                foreach ($questions as $question) {
                    $count = Log\Slide::ofResult($result->id)
                        ->ofType('question')
                        ->where('reference_id', '=', $question->question_id)
                        ->count('id');

                    $question->read_count = $count;
                    $question->save();
                }
            }
        }
    }

    protected function count_passage_reads(Result $result)
    {
        $slides = Log\Slide::ofResult($result->id)->get();

        $questionR = new Result\Question();

        foreach ($slides as $slide) {
            switch ($slide->type) {
                case 'passage':
                    $passageR = Result\Passage::ofResult($result->id)->where('passage_id', '=', $slide->reference_id)->first();

                    if ($questionR->passage_id == $passageR->id) {
                        $questionR->passage_count++;
                        $questionR->save();
                    }

                    $questionR = new Result\Question();

                    break;
                case 'question':
                    $questionR = Result\Question::ofResult($result->id)->where('question_id', '=', $slide->reference_id)->first();

                    break;
                default:

            }
        }
    }

    /**
     * Calculate the scores.
     *
     * @param  Result  $result
     *
     * @return Result
     */
    protected function calculate_scores(Result $result)
    {
        $sections = Result\Section::ofResult($result->id)->get();

        /** @var Result\Section $section */
        foreach ($sections as $section) {
            $passages = Result\Passage::ofSection($section->id)->get();

            /** @var Result\Passage $passage */
            foreach ($passages as $passage) {
                $questions = Result\Question::ofPassage($passage->id)->get();

                /** @var Result\Question $question */
                foreach ($questions as $question) {
                    $corrects = Result\Choice::correct($question->id)->get();
                    $answer   = Result\Answer::ofQuestion($question->id)->first();

                    $section->total += $question->points;

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

        switch ($scoring->slug) {
            case 'sat':
                $score = ScoringSAT::calculate($result);

                $ssr_obj               = new Result\Score();
                $ssr_obj->name         = 'English Language';
                $ssr_obj->result_id    = $result->id;
                $ssr_obj->score_raw    = null;
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
