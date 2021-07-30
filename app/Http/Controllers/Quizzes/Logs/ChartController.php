<?php

namespace App\Http\Controllers\Quizzes\Logs;

use App\Http\Controllers\Controller;
use App\Models\Quizzes\Log;
use App\Models\Quizzes\Log\Answer;
use App\Models\Quizzes\Log\Blink;
use App\Models\Quizzes\Log\Dilation;
use App\Models\Quizzes\Log\Emotion;
use App\Models\Quizzes\Log\Pulse;
use App\Models\Quizzes\Log\Slide;
use App\Models\Quizzes\Log\Slouch;
use App\Models\Quizzes\Result;
use App\Models\Quizzes\Types\Problem;
use Khill\Duration\Duration;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;
use stdClass as StdClass;

class ChartController extends Controller
{
    public function list($id)
    {
        $slidesRs  = Slide::ofResult($id)->orderBy('id', 'asc')->get()->makeHidden(['result_id', 'created_at']);
        $answers   = Answer::ofResult($id)->orderBy('id', 'asc')->get()->makeHidden(['result_id', 'created_at']);
        $pulses    = Pulse::ofResult($id)->orderBy('id', 'asc')->get()->makeHidden(['result_id', 'created_at']);
        $blinks    = Blink::ofResult($id)->orderBy('id', 'asc')->get()->makeHidden(['result_id', 'created_at']);
        $dilations = Dilation::ofResult($id)->orderBy('id', 'asc')->get()->makeHidden(['result_id', 'created_at']);
        $slouches  = Slouch::ofResult($id)->orderBy('id', 'asc')->get()->makeHidden(['result_id', 'created_at']);

        $emotionsRs = [
            Emotion::PINCHED_EYEBROWS => Emotion::ofResult($id)->ofEmotion(Emotion::PINCHED_EYEBROWS)->orderBy('id', 'asc')->get()->makeHidden(['result_id', 'created_at']),
            Emotion::PINCHED_NOSE     => Emotion::ofResult($id)->ofEmotion(Emotion::PINCHED_NOSE)->orderBy('id', 'asc')->get()->makeHidden(['result_id', 'created_at']),
            Emotion::WIDE_EYES        => Emotion::ofResult($id)->ofEmotion(Emotion::WIDE_EYES)->orderBy('id', 'asc')->get()->makeHidden(['result_id', 'created_at']),
            Emotion::SMILE            => Emotion::ofResult($id)->ofEmotion(Emotion::SMILE)->orderBy('id', 'asc')->get()->makeHidden(['result_id', 'created_at']),
            Emotion::FROWN            => Emotion::ofResult($id)->ofEmotion(Emotion::FROWN)->orderBy('id', 'asc')->get()->makeHidden(['result_id', 'created_at']),
            Emotion::RAISED_EYEBROWS  => Emotion::ofResult($id)->ofEmotion(Emotion::RAISED_EYEBROWS)->orderBy('id', 'asc')->get()->makeHidden(['result_id', 'created_at']),
            Emotion::RELAXED          => Emotion::ofResult($id)->ofEmotion(Emotion::RELAXED)->orderBy('id', 'asc')->get()->makeHidden(['result_id', 'created_at']),
        ];

        $emotions = [[], [], [], [], [], [], []];

        $last = Pulse::ofResult($id)->orderBy('id', 'desc')->first();

        $slides = array_fill(1, $last->clock, null);

        foreach ($slides as $k => $slide) {
            $slide        = new StdClass();
            $slide->x     = date('Y-m-d H:i:s', $k);
            $slide->y     = -10;
            $slide->type  = '';
            $slide->title = '';
            $slides[$k]   = $slide;
        }

        foreach ($slidesRs as $slide) {
            $object       = new StdClass();
            $object->x    = date('Y-m-d H:i:s', $slide->clock);
            $object->y    = 20;
            $object->type = ucfirst($slide->type);

            switch ($slide->type) {
                default:
                    break;
                case 'passage':
                    $passage       = Result\Passage::ofResult($id)->where('passage_id', '=', $slide->reference_id)->first();
                    $object->title = $passage->name;
                    break;
                case 'question':
                    $question      = Result\Question::ofResult($id)->where('question_id', '=', $slide->reference_id)->first();
                    $object->title = $question->question;
            }

            $slides[$slide->clock] = $object;
        }

        foreach ($emotionsRs as $k => $data) {
            $emotions[$k] = array_fill(1, $last->clock, -10);
            if (count($data) > 0) {
                foreach ($data as $emotion) {
                    $emotions[$k][$emotion->clock] = $emotion->probability >= 60 ? round($emotion->probability) : -10;
                }
            }
        }


        $value = [
            'labels' => [],
            'data'   => [
                'slides'   => [
                    'slides'       => [],
                    'bgColors'     => [],
                    'borderColors' => [],
                ],
                'answers'  => [
                    'answers'      => [],
                    'bgColors'     => [],
                    'borderColors' => [],
                ],
                'pulse'    => [],
                'blink'    => [],
                'iris'     => [
                    'width'  => [],
                    'height' => [],
                    'area'   => [],
                ],
                'emotions' => [
                    Emotion::SMILE            => [],
                    Emotion::FROWN            => [],
                    Emotion::RAISED_EYEBROWS  => [],
                    Emotion::PINCHED_EYEBROWS => [],
                    Emotion::WIDE_EYES        => [],
                    Emotion::RELAXED          => [],
                ],
                'slouch'   => [],
            ],
        ];

        foreach ($slides as $slide) {
            $bgColor     = '#8CBED6';
            $borderColor = '#8CBED6';

            switch ($slide->type) {
                case 'Form':
                    $bgColor     = '#000066';
                    $borderColor = '#000066';
                    break;
                case 'Passage':
                    $bgColor     = '#7F00FF';
                    $borderColor = '#7F00FF';
                    break;
                case 'Question':
                default:

            }

            $value['data']['slides']['bgColors'][]     = $bgColor;
            $value['data']['slides']['borderColors'][] = $borderColor;

            $value['data']['slides']['slides'][] = $slide;
        }

        /** @var Answer $answer */
        foreach ($answers as $answer) {
            $object    = new StdClass();
            $object->x = date('Y-m-d H:i:s', $answer->clock);
            $object->y = 10;

            $object->s   = '';
            $object->sn  = '';
            $object->swt = '';
            $object->swr = '';
            $object->sws = '';
            $object->swp = '';

            /** @var Result\Question $question */
            $question = Result\Question::ofResult($id)->where('question_id', '=', $answer->question_id)->first();
            $corrects = Result\Choice::correct($question->id)->get();

            $isCorrect = false;

            /** @var Result\Choice $correct */
            foreach ($corrects as $correct) {
                if ($answer->choice_id === $correct->choice_id) {
                    $question->score = $question->points;

                    $isCorrect = true;
                    break;
                }
            }

            $object->q   = $question->question;
            $object->a   = $answer->choice_name;
            $object->c   = $isCorrect;
            $object->qwt = '';
            $object->qwr = '';
            $object->qws = '';
            $object->qwp = '';

            /**
             * $object->s   = $answers_result[$k]->section_id;
             * $object->sn  = $answers_result[$k]->section_name;
             * $object->swt = $answers_result[$k]->section_words;
             * $object->swr = $answers_result[$k]->section_read;
             * $object->sws = $answers_result[$k]->section_read_score;
             * $object->swp = $answers_result[$k]->section_read_percentage;
             *
             * $object->q   = $answers_result[$k]->question_name;
             * $object->a   = html_entity_decode($answers_result[$k]->answer);
             * $object->c   = $answers_result[$k]->correct;
             * $object->qwt = $answers_result[$k]->question_words;
             * $object->qwr = $answers_result[$k]->question_read;
             * $object->qws = $answers_result[$k]->question_read_score;
             * $object->qwp = $answers_result[$k]->question_read_percentage;
             **/

            $value['data']['answers']['answers'][]      = $object;
            $value['data']['answers']['bgColors'][]     = $object->c ? '#228b22' : '#df4145';
            $value['data']['answers']['borderColors'][] = $object->c ? '#228b22' : '#df4145';

        }

        /**
         * @var int   $k
         * @var Pulse $pulse
         */
        foreach ($pulses as $k => $pulse) {
            $value['labels'][]        = date('Y-m-d H:i:s', $pulse->clock);
            $value['data']['pulse'][] = [
                'x' => date('Y-m-d H:i:s', $pulse->clock),
                'y' => $pulse->rate,
            ];
        }

        /**
         * @var int   $k
         * @var Blink $blink
         */
        foreach ($blinks as $k => $blink) {
            $value['data']['blink'][] = [
                'x' => date('Y-m-d H:i:s', $blink->clock),
                'y' => $blink->count > 0 ? 20 : -10,
                'r' => $blink->count > 0 ? (floor(log($blink->duration))) : 0,
                'd' => round($blink->duration / 10) * 10,
            ];
        }

        /**
         * @var int      $k
         * @var Dilation $dilation
         */
        foreach ($dilations as $k => $dilation) {
            $x = date('Y-m-d H:i:s', $dilation->clock);

            $value['data']['iris']['width'][]  = [
                'x' => $x,
                'y' => round($dilation->width),
            ];
            $value['data']['iris']['height'][] = [
                'x' => $x,
                'y' => round($dilation->height),
            ];
            $value['data']['iris']['area'][]   = [
                'x' => $x,
                'y' => round($dilation->area),
            ];
        }

        foreach ($emotions as $k => $emotion) {
            foreach ($emotion as $m => $certainty) {
                $value['data']['emotions'][$k][] = [
                    'x' => date('Y-m-d H:i:s', $m),
                    'y' => $certainty,
                ];
            }
        }

        /**
         * @var int    $k
         * @var Slouch $slouch
         */
        foreach ($slouches as $k => $slouch) {
            $value['data']['slouch'][] = [
                'x' => date('Y-m-d H:i:s', $slouch->clock),
                'y' => $slouch->slouch_count > 0 ? 2 : null,
                'd' => round($slouch->slouch_duration),
            ];
        }

        return $value;
    }

    public function chart($id)
    {
        $result = Result::find($id);

        $data = [
            'labels' => [],
            'values' => [
                'pulse' => [],
                'blink' => [],
            ],
        ];

        $slides    = Slide::ofResult($id)->orderBy('id', 'asc')->get()->makeHidden(['result_id', 'created_at']);
        $answers   = Answer::ofResult($id)->orderBy('id', 'asc')->get()->makeHidden(['result_id', 'created_at']);
        $pulses    = Pulse::ofResult($id)->orderBy('id', 'asc')->get()->makeHidden(['result_id', 'created_at']);
        $blinks    = Blink::ofResult($id)->orderBy('id', 'asc')->get()->makeHidden(['result_id', 'created_at']);
        $dilations = Dilation::ofResult($id)->orderBy('id', 'asc')->get()->makeHidden(['result_id', 'created_at']);
        $slouches  = Slouch::ofResult($id)->orderBy('id', 'asc')->get()->makeHidden(['result_id', 'created_at']);

        foreach ($pulses as $pulse) {
            $data['values']['pulse'][] = [$pulse->clock, $pulse->rate];
        }

        foreach ($blinks as $blink) {
            if ($blink->count > 0) {
                $data['values']['blink'][] = [$blink->clock, 1, floor($blink->duration / 100)];
            }
        }

        return $data;

    }

    public function summary($id)
    {
        $result = Result::find($id);
        $log    = Log::find($result->id);

        // Grit
        // -----------------------------------

        $grit = [
            'grit'   => [
                'total' => 0,
                'score' => 0,

            ],
            'points' => [
                'total' => 0,
            ],
        ];

        $grit_questions = Result\Question::where('quizzes_results_questions.result_id', '=', $result->id)
            ->leftJoin('quizzes_results_problem_types', 'quizzes_results_questions.id', '=', 'quizzes_results_problem_types.question_id')
            ->select('quizzes_results_questions.*')
            ->where('quizzes_results_problem_types.key', '=', 'grit') // TODO: Include in the settings as Grit Tag
            ->get();

        // If questions tagged as grit is less than the specified value,
        // get a total score.
        // TODO: Include in the settings as Grit Threshold
        if ($grit_questions->count() < 10) {
            $grit_questions = Result\Question::where('quizzes_results_questions.result_id', '=', $result->id)->get();
        }

        foreach ($grit_questions as $question) {
            $grit['grit']['total'] += $question->points;
            $grit['grit']['score'] += $question->score;
        }

        $grit['grit']['percent']  = round(($grit['grit']['score'] / $grit['grit']['total']) * 100);
        $grit['grit']['perdecem'] = round(($grit['grit']['score'] / $grit['grit']['total']) * 10);

        $grit['points']['total'] = $grit['grit']['percent'];


        // Attention to Detail
        // -----------------------------------

        $attention = [
            'attention' => [
                'total' => 0,
                'score' => 0,
            ],
            'points'    => [
                'total' => 0,
            ],
        ];

        $questions = Result\Question::where('quizzes_results_questions.result_id', '=', $result->id)
            ->leftJoin('quizzes_results_problem_types', 'quizzes_results_questions.id', '=', 'quizzes_results_problem_types.question_id')
            ->select('quizzes_results_questions.*')
            ->where('quizzes_results_problem_types.key', '=', 'close-reading') // TODO: Include in the settings as Close Reading Tag
            ->get();

        foreach ($questions as $question) {
            $attention['attention']['total'] += $question->points;
            $attention['attention']['score'] += $question->score;
        }

        $attention['attention']['percent']  = round(($attention['attention']['score'] / $attention['attention']['total']) * 100);
        $attention['attention']['perdecem'] = round(($attention['attention']['score'] / $attention['attention']['total']) * 10);

        $attention['points']['total'] = $attention['attention']['percent'];


        // Vertical Fluidity
        // -----------------------------------

        $count_total = Result\Question::where('quizzes_results_questions.result_id', '=', $result->id)
            ->leftJoin('quizzes_results_sections', 'quizzes_results_questions.section_id', '=', 'quizzes_results_sections.id')
            ->leftJoin('quizzes_types_sections', 'quizzes_results_sections.type_id', '=', 'quizzes_types_sections.id')
            ->select('quizzes_results_questions.*')
            ->where('quizzes_types_sections.slug', '=', 'reading') // TODO: Include in the settings as Reading Comprehension Tag
            ->count();

        $count_repeat = Result\Question::where('quizzes_results_questions.result_id', '=', $result->id)
            ->leftJoin('quizzes_results_sections', 'quizzes_results_questions.section_id', '=', 'quizzes_results_sections.id')
            ->leftJoin('quizzes_types_sections', 'quizzes_results_sections.type_id', '=', 'quizzes_types_sections.id')
            ->select('quizzes_results_questions.*')
            ->where('quizzes_types_sections.slug', '=', 'reading') // TODO: Include in the settings as Reading Comprehension Tag
            ->where('quizzes_results_questions.passage_count', '>', 0)
            ->count();

        $vertical_fluidty = [
            'vertical_fluidity' => [
                'total'    => $count_total,
                'score'    => $count_repeat,
                'percent'  => $count_total > 0 ? round(($count_repeat / $count_total) * 100) : 0,
                'perdecem' => $count_total > 0 ? round(($count_repeat / $count_total) * 10) : 0,
            ],
            'points'            => [
                'total' => 0,
            ],
        ];

        $vertical_fluidty['points']['total'] = $vertical_fluidty['vertical_fluidity']['percent'];


        // Equanimity (Composure)
        // -----------------------------------

        if ($log->pulse_status == 0) {
            $equanimity = [
                'time_spent' => [
                    'total'    => 0,
                    'composed' => [
                        'points'   => 0,
                        'percent'  => 0,
                        'perdecem' => 0,
                    ],
                    'ruffled'  => [
                        'points'   => 0,
                        'percent'  => 0,
                        'perdecem' => 0,
                    ],
                ],
                'chart'      => [
                    'labels' => null,
                    'values' => null,
                ],
                'recovery'   => [
                    'missing'   => 0,
                    'recovered' => 0,
                    'average'   => 0,
                ],
                'pulse'      => [
                    'points' => 0,
                ],
                'points'     => [
                    'time_spent' => 0,
                    'pulse'      => 0,
                    'total'      => 0,
                ],
            ];
        } else {
            // -- Composure Test --

            $home_slide = Slide::ofType('home')->orderBy('clock', 'ASC')->first();
            $base_pulse = Pulse::ofResult($result->id)->where('clock', '=', $home_slide->clock)->first();

            $threshold = ceil($base_pulse->rate + ($base_pulse->rate * 0.06)); // TODO: Add the figure in the settings

            $slides = Slide::ofResult($result->id)->ofType('question')->where('duration', '>', 30)->get(); // TODO: Add the duration in the settings

            $total    = 0;
            $composed = 0;
            $ruffled  = 0;

            foreach ($slides as $slide) {
                $pulse = Pulse::ofResult($result->id)->where('clock', $slide->clock)->first();

                if ($threshold < $pulse->rate) {
                    $ruffled++;
                } else {
                    $composed++;
                }

                $total++;
            }

            // -- Recovery --

            $questions = Result\Question::ofResult($result->id)->get();

            $missing    = [];
            $recoveries = [];

            foreach ($questions as $question) {
                $slides = Slide::ofResult($result->id)
                    ->where('reference_id', $question->question_id)
                    ->orderBy('clock', 'ASC')
                    ->get();

                $unanswered = null;

                foreach ($slides as $slide) {
                    if ($slide->answered == 0) {
                        $unanswered = $slide;

                        continue;
                    }

                    if ($unanswered && $slide->answered == 1) {
                        $recovered = new StdClass();

                        $recovered->question = $question->id;
                        $recovered->time     = $slide->clock - $unanswered->clock;

                        $recoveries[] = $recovered;
                        $unanswered   = null;

                        break;
                    }
                }

                if ($unanswered) {
                    $missing[]  = $question;
                    $unanswered = null;
                }
            }

            if (count($recoveries) > 0) {
                $recovery_time = array_reduce($recoveries, function ($carry, $item) {
                    return $carry + $item->time;
                });
            } else {
                $recovery_time = 0;
            }

            // -- Pulse --

            $pulse_points = 100;
            $continuous   = 0;
            $labels       = [];
            $values       = [];

            $pulses = Pulse::ofResult($result->id)->where('clock', '>=', $home_slide->clock)->get();

            foreach ($pulses as $pulse) {
                $labels[] = date('H:i:s', $pulse->clock);
                $values[] = $pulse->rate;

                if ($threshold < $pulse->rate) {
                    $pulse_points -= 5; // TODO: Add to settings
                    $continuous++;
                } else {
                    $continuous = 0;
                }

                if ($continuous > 60) {
                    $pulse_points = 0;
                    continue;
                }
            }

            $equanimity = [
                'time_spent' => [
                    'total'    => $total,
                    'composed' => [
                        'points'   => $composed,
                        'percent'  => $total > 1 ? round(($composed / $total) * 100) : 100,
                        'perdecem' => $total > 1 ? round(($composed / $total) * 10) : 10,
                    ],
                    'ruffled'  => [
                        'points'   => $ruffled,
                        'percent'  => $total > 1 ? round(($ruffled / $total) * 100) : 0,
                        'perdecem' => $total > 1 ? round(($ruffled / $total) * 10) : 0,
                    ],
                ],
                'chart'      => [
                    'labels' => $labels,
                    'values' => $values,
                ],
                'recovery'   => [
                    'missing'   => count($missing),
                    'recovered' => count($recoveries),

                ],
                'pulse'      => [
                    'points' => $pulse_points,
                ],
            ];

            $duration = new Duration();
            $average  = (count($recoveries) > 0) ? round($recovery_time / count($recoveries)) : '-';

            $average_array = [
                'seconds' => (count($recoveries) > 0) ? $average : '-',
                'hms'     => (count($recoveries) > 0) ? $duration->formatted($average) : '-',
                'human'   => (count($recoveries) > 0) ? $duration->humanize($average) : '-',
            ];

            $equanimity['recovery']['average'] = $average_array;

            $equanimity['points'] = [
                'time_spent' => (($equanimity['time_spent']['composed']['percent'] / 100) * 75),
                'pulse'      => (($equanimity['pulse']['points'] / 100) * 25),
            ];

            $equanimity['points']['total'] = round($equanimity['points']['time_spent'] + $equanimity['points']['pulse']);
        }


        // Time Awareness
        // -----------------------------------

        // -- Questions Answered --

        $answered = [
            'total'    => $result->questions_count,
            'answered' => $result->questions_answered,
            'skipped'  => $result->questions_skipped,
            'percent'  => round(($result->questions_answered / $result->questions_count) * 100),
            'perdecem' => round(($result->questions_answered / $result->questions_count) * 10),
        ];

        // -- Time Spent on Last Questions --

        // Last questions count
        $lqc = round($result->questions_count * 0.2);

        $sum = Slide::ofResult($result->id)->ofType('question')->limit($lqc)->sum('duration');

        $duration = new Duration();

        $time_spent = [
            'seconds' => $sum,
            'hms'     => $duration->formatted($sum),
            'human'   => $duration->humanize($sum),
        ];

        // -- Points --

        $fqc = round($result->questions_count * 0.8);  // First 80% Questions Count
        $f95 = round($result->questions_count * 0.95); // First 95% of Questions Count

        $time_limit   = Result\Section::ofResult($result->id)->sum('time_limit');
        $first_eighty = $this->addQuestionTimeSpent($result, $fqc);
        $deduction    = $this->countQuestionsTimeSpent($result, $f95, 180); // TODO: Add to settings.

        $points = ((($time_limit * 60) * 0.75 / $first_eighty) * 100) - $deduction;
        $points = ($points < 0) ? 0 : $points;

        $time_awareness = [
            'questions_answered' => $answered,
            'time_spent'         => $time_spent,
        ];

        $time_awareness['points'] = [
            'questions_answered' => ($time_awareness['questions_answered']['percent'] == 100 ? 100 : 0) * 0.25,
            'time_spent'         => ($points > 100 ? 100 : $points) * 0.75,
        ];

        $time_awareness['points']['total'] = $time_awareness['points']['questions_answered'] + $time_awareness['points']['time_spent'];


        // General Knowledge
        // -----------------------------------

        $sections = Result\Section::ofResult($id)->get();

        $sections_sortable = [];

        $subjects_raw   = [];
        $subjects_chart = [
            'labels' => [],
            'values' => [],
        ];
        $subjects_comp  = [
            'best'  => [],
            'worst' => [],
        ];

        foreach ($sections as $section) {
            $subject = new StdClass();

            $subject->id    = $section->id;
            $subject->name  = $section->name;
            $subject->total = 0;
            $subject->score = 0;

            $questions = Result\Question::ofResult($id)->ofSection($section->id)->get();

            foreach ($questions as $question) {
                $subject->total += $question->points;
                $subject->score += $question->score;
            }

            $subject->percent  = round(($subject->score / $subject->total) * 100);
            $subject->perdecem = round(($subject->score / $subject->total) * 10);

            $sections_sortable[] = clone $subject;

            $subjects_raw[] = $subject;

            $subjects_chart['labels'][] = explode(' ', $subject->name);
            $subjects_chart['values'][] = $subject->percent;

            // Problem Types
            // -----------------------------------

            $subject->problem_types = [];

            $probtypes_rs = Result\ProblemType::ofSection($section->id)
                ->select('key')
                ->distinct()
                ->orderBy('key', 'ASC')
                ->get();

            foreach ($probtypes_rs as $probtype) {
                $ptObj = Problem::find($probtype->key);

                if (!$ptObj) {
                    continue;
                }

                $topic        = new StdClass();
                $topic->name  = $ptObj->name;
                $topic->total = 0;
                $topic->score = 0;

                $questions = Result\Question::where('quizzes_results_questions.result_id', '=', $result->id)
                    ->where('quizzes_results_questions.section_id', '=', $section->id)
                    ->leftJoin('quizzes_results_problem_types', 'quizzes_results_questions.id', '=', 'quizzes_results_problem_types.question_id')
                    ->select('quizzes_results_questions.*')
                    ->where('quizzes_results_problem_types.key', '=', $probtype->key)
                    ->get();

                foreach ($questions as $question) {
                    $topic->total += $question->points;
                    $topic->score += $question->score;
                }

                $topic->percent  = round(($topic->score / $topic->total) * 100);
                $topic->perdecem = round(($topic->score / $topic->total) * 10);

                $subject->problem_types[] = $topic;
            }
        }

        // -- Best and Worst Subjects --

        $sections_best = $sections_worst = $sections_sortable;

        usort($sections_best, function ($a, $b) {
            return $a->score < $b->score;
        });
        usort($sections_worst, function ($a, $b) {
            return $a->score > $b->score;
        });

        $subjects_comp['best']  = array_slice($sections_best, 0, 1);  // TODO: Add to settings
        $subjects_comp['worst'] = array_slice($sections_worst, 0, 1); // TODO: Add to settings


        // Difficulty
        // -----------------------------------

        $questions_rs = Result\Question::ofResult($id)->get();
        $diff         = range(1, 5);

        $questions    = [];
        $difficulties = [
            'all'      => [
                'levels' => [],
                'total'  => [
                    'score'    => 0,
                    'points'   => 0,
                    'percent'  => 0,
                    'perdecem' => 0,
                ],
            ],
            'two-five' => [
                'levels' => [],
                'total'  => [
                    'score'    => 0,
                    'points'   => 0,
                    'percent'  => 0,
                    'perdecem' => 0,
                ],
            ],
            'points'   => [
                'total' => 0,
            ],
        ];

        foreach ($questions_rs as $question) {
            if (!isset($questions[$question->difficulty])) {
                $questions[$question->difficulty] = [];
            }

            $questions[$question->difficulty][] = $question;
        }

        foreach ($diff as $d) {
            if (!isset($questions[$d])) {
                continue;
            }

            if (count($questions[$d]) < 1) {
                continue;
            }

            $level = new StdClass();

            $level->name   = 'Level ' . $d;
            $level->number = $d;
            $level->total  = 0;
            $level->score  = 0;

            foreach ($questions[$d] as $question) {
                $level->total += $question->points;
                $level->score += $question->score;
            }

            $level->percent  = round(($level->score / $level->total) * 100);
            $level->perdecem = round(($level->score / $level->total) * 10);

            $difficulties['all']['levels'][]        = $level;
            $difficulties['all']['total']['score']  += $level->score;
            $difficulties['all']['total']['points'] += $level->total;

            if ($d > 1) {
                $difficulties['two-five']['levels'][]        = $level;
                $difficulties['two-five']['total']['score']  += $level->score;
                $difficulties['two-five']['total']['points'] += $level->total;
            }
        }

        $difficulties['all']['total']['percent']  = round(($difficulties['all']['total']['score'] / $difficulties['all']['total']['points']) * 100);
        $difficulties['all']['total']['perdecem'] += round(($difficulties['all']['total']['score'] / $difficulties['all']['total']['points']) * 10);

        $difficulties['two-five']['total']['percent']  = round(($difficulties['two-five']['total']['score'] / $difficulties['two-five']['total']['points']) * 100);
        $difficulties['two-five']['total']['perdecem'] = round(($difficulties['two-five']['total']['score'] / $difficulties['two-five']['total']['points']) * 10);


        // Performance By Type
        // -----------------------------------

        $performance_by_type = [
            'labels' => [],
            'data'   => [],
        ];

        $scores = Result\Score::ofResult($result->id)->get();

        foreach ($scores as $score) {
            $percentage = round(($score->score_raw / $score->score_raw_total) * 100);

            $performance_by_type['labels'][] = explode(' ', $score->name);
            $performance_by_type['data'][]   = $percentage;
        }


        // Overall
        // -----------------------------------

        $score = ($result->score / $result->target) * 100;
        $score = $score > 100 ? 100 : $score;

        $overall = [
            'score'      => $score * 0.50,
            'factors'    => [
                'grit'              => $grit['points']['total'] * 0.08,
                'attention'         => $attention['points']['total'] * 0.08,
                'vertical_fluidity' => $vertical_fluidty['points']['total'] * 0.08,
                'time_awareness'    => $time_awareness['points']['total'] * 0.08,
                'equanimity'        => $equanimity['points']['total'] * 0.08,
            ],
            'difficulty' => $difficulties['two-five']['total']['percent'] * 0.10,
        ];

        $sum       = 0;
        $array_obj = new RecursiveIteratorIterator(new RecursiveArrayIterator($overall));
        foreach ($array_obj as $value) {
            $sum += $value;
        }

        $overall['total'] = round($sum);


        // Return results
        // -----------------------------------

        return [
            'status'            => [
                'pulse'    => $log->pulse_status,
                'dilation' => $log->iris_status,
                'blinks'   => $log->blinks_status,
                'slouch'   => $log->slouch_status,
            ],
            'questions'         => [
                $result->answers_correct,
                $result->answers_incorrect,
                $result->questions_skipped,
            ],
            'scores'            => [
                'total'  => $result->total,
                'earned' => $result->score,
            ],
            'grit'              => $grit,
            'attention'         => $attention,
            'vertical_fluidity' => $vertical_fluidty,
            'equanimity'        => $equanimity,
            'general_knowledge' => [
                'raw'        => $subjects_raw,
                'chart'      => $subjects_chart,
                'comparison' => $subjects_comp,
            ],
            'time_awareness'    => $time_awareness,
            'difficulty'        => $difficulties,
            'performance'       => [
                'type' => $performance_by_type,
            ],
            'overall'           => $overall,
        ];

    }

    /**
     * @param  Result  $result
     * @param  int     $limit
     *
     * @return int|mixed
     */
    protected function addQuestionTimeSpent(Result $result, $limit)
    {
        $sum   = 0;
        $count = 0;

        $sections = Result\Section::ofResult($result->id)->orderBy('ordering', 'ASC')->get();

        foreach ($sections as $section) {
            $passages = Result\Passage::ofSection($section->id)->orderBy('ordering', 'ASC')->get();
            foreach ($passages as $passage) {
                $questions = Result\Question::ofPassage($passage->id)->orderBy('ordering', 'ASC')->get();
                foreach ($questions as $question) {
                    if ($count >= $limit) {
                        break;
                    }

                    $question_sum = Slide::ofType('question')->where('reference_id', '=', $question->question_id)->sum('duration');
                    $sum          += $question_sum;

                    $count++;
                }
            }
        }

        return $sum;
    }

    /**
     * Count Questions that spend more than time alloted.
     *
     * @param  Result  $result
     * @param  int     $limit
     * @param  int     $time
     *
     * @return int
     */
    protected function countQuestionsTimeSpent(Result $result, $limit = 10, $time = 180)
    {
        $count = 0;

        $sections = Result\Section::ofResult($result->id)->orderBy('ordering', 'ASC')->get();

        foreach ($sections as $section) {
            $passages = Result\Passage::ofSection($section->id)->orderBy('ordering', 'ASC')->get();
            foreach ($passages as $passage) {
                $questions = Result\Question::ofPassage($passage->id)->orderBy('ordering', 'ASC')->get();
                foreach ($questions as $question) {
                    if ($count >= $limit) {
                        break;
                    }

                    $question_sum = Slide::ofType('question')->where('reference_id', '=', $question->question_id)->sum('duration');
                    if ($question_sum > $time) {
                        $count++;
                    }
                }
            }
        }

        return $count;
    }
}
