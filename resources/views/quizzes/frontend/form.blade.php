@extends('layout-frontend')

@section('title', $quiz->name)

@section('stylesheet', asset('/css/frontend.css'))

@section('content')
    <canvas id="canvas-calibration" width="500" height="500" style="cursor:crosshair;"></canvas>

    <div class="div-calibration">
        <input type="button" class="calibration" id="pt1" value="">
        <input type="button" class="calibration" id="pt2" value="">
        <input type="button" class="calibration" id="pt3" value="">
        <input type="button" class="calibration" id="pt4" value="">
        <input type="button" class="calibration" id="pt5" value="">
        <input type="button" class="calibration" id="pt6" value="">
        <input type="button" class="calibration" id="pt7" value="">
        <input type="button" class="calibration" id="pt8" value="">
        <input type="button" class="calibration" id="pt9" value="">
    </div>

    <div id="wrapper-video" class="position-fixed d-none" style="top: 5rem; left: 1rem">
        <video id="video-cap" width="320" height="240" controls autoplay playsinline></video>
    </div>

    <div class="content-loading container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="progress mt-5">
                    <div class="progress-bar progress-bar-striped progress-bar-animated w-100" role="progressbar"></div>
                </div>
                <p class="text-center mt-4">Loading elements... please wait.</p>
            </div>
        </div>
    </div>

    <div class="content-calibration container-fluid">
        <div class="row">
            <div class="col-md-8"></div>
        </div>
    </div>

    <form id="quiz" class="h-100 d-none justify-content-center align-items-center" name="quiz">
        <div class="border rounded quiz-area w-90 h-90">
            <div class="container-fluid h-100">
                <div id="slides-area" class="row h-100 slides-area">
                    <div class="col-9 slides-panel rounded-left d-flex flex-column p-0">
                        <?php $navbox = ''; $index = 0; $qstn = 1; ?>
                        <div class="slides w-100 flex-fill">
                            <div class="slide slide-title slide-quiz-name d-flex justify-content-center align-items-center"
                                 data-index="<?php echo $index; ?>"
                                 data-slide-group="init"
                                 data-slide-type="home"
                                 data-slide-flag="s"
                                 data-section-id="-"
                                 data-id="0">
                                <h1 class="mb-0 display-3 font-weight-bold text-center"><?php echo $quiz->name; ?></h1>
                                <?php $navbox .= '<div class="navbox-section" data-section-id="-">'; ?>
                                <?php $navbox .= '<button type="button" class="btn btn-sm btn-primary btn-navbox active" data-index="' . ($index++) . '"><i class="fas fa-fw fa-home"></i></button>';?>
                            </div>

                            <div class="slide slide-details d-flex justify-content-center align-items-center"
                                 data-index="<?php echo $index; ?>"
                                 data-slide-group="init"
                                 data-slide-type="form"
                                 data-slide-flag=""
                                 data-section-id="-"
                                 data-id="0">
                                <div class="details w-75">
                                    <div class="question text-center">
                                        <p>Please fill in the details.</p>
                                    </div>
                                    <div class="answer">
                                        <div class="form-group row">
                                            <label for="name" class="col-sm-2 col-form-label">Your Name</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" id="name" name="name">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="email" class="col-sm-2 col-form-label">Email</label>
                                            <div class="col-sm-10">
                                                <input type="email" class="form-control" id="email" name="email">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php $navbox .= '<button type="button" class="btn btn-sm btn-secondary btn-navbox" data-index="' . ($index++) . '">F</button>'; ?>
                            </div>

                            <div class="slide slide-details d-flex justify-content-center align-items-center"
                                 data-index="<?php echo $index; ?>"
                                 data-slide-type="form"
                                 data-section-id="-"
                                 data-slide-flag="<?php echo (empty($quiz->description)) ? 'e' : ''?>"
                                 data-id="0">
                                <div class="details w-75">
                                    <h2 class="display-4 text-center">Your target score?</h2>
                                    <div class="text-center mt-3">
                                        <input type="number" class="form-control form-control-lg text-center w-50 mx-auto" id="target_score" name="target_score" min="70" max="100" >
                                    </div>
                                </div>
                                <?php $navbox .= '<button type="button" class="btn btn-sm btn-secondary btn-navbox" data-index="' . ($index++) . '">F</button>'; ?>
                            </div>

                            <?php if (!empty($quiz->description)) : ?>
                            <div class="slide slide-description slide-quiz-description"
                                 data-index="<?php echo $index; ?>"
                                 data-slide-type="quiz-description"
                                 data-slide-flag="e"
                                 data-section-id="-"
                                 data-id="0">
                                <?php echo $quiz->description; ?>
                                <?php $navbox .= '<button type="button" class="btn btn-sm btn-secondary btn-navbox" data-index="' . ($index++) . '">&nbsp;</button>'; ?>

                            </div>
                            <?php endif; ?>

                            <?php $navbox .= '</div>'; ?>

                            <?php foreach ($quiz->sections as $section) : ?>
                            <?php $navbox .= '<div class="navbox-section" data-section-id="' . $section->id . '">'; ?>

                            <?php if (count($quiz->sections) > 1) : ?>

                            <div class="slide slide-section slide-section-name d-flex justify-content-center align-items-center"
                                 data-index="<?php echo $index; ?>"
                                 data-slide-type="section"
                                 data-section-id="<?php echo $section->id; ?>"
                                 data-id="<?php echo $section->id; ?>"
                                 data-slide-flag="s"
                                 >
                                <h2 class="mb-0 display-4 font-weight-bold text-center"><?php echo $section->name; ?></h2>

                                <?php $navbox .= '<button type="button" class="btn btn-sm btn-secondary btn-navbox" data-index="' . ($index++) . '">S</button>';?>
                            </div>

                            <?php if (!empty($section->description)) : ?>
                            <div class="slide slide-section slide-section-description" data-index="<?php echo $index; ?>"
                                 data-slide-type="section-description"
                                 data-section-id="<?php echo $section->id; ?>"
                                 data-id="<?php echo $section->id; ?>"
                                 >
                                <?php echo $section->description; ?>
                                <?php $navbox .= '<button type="button" class="btn btn-sm btn-secondary btn-navbox"  data-index="' . ($index++) . '">&nbsp;</button>';?>
                            </div>
                            <?php endif; ?>
                            <?php endif; ?>

                            <?php $plen = count($section->passages); ?>
                            <?php foreach ($section->passages as $p => $passage) : ?>

                            <?php if (!empty($passage->content)) : ?>
                            <div class="slide slide-passage" data-index="<?php echo $index; ?>"
                                 data-slide-type="passage"
                                 data-section-id="<?php echo $section->id; ?>"
                                 data-id="<?php echo $passage->id; ?>"
                                 data-qt-start="true">
                                <h3 class="h1 text-center mb-3"><?php echo $passage->name; ?></h3>
                                <?php if (!empty($passage->description)) : ?>
                                <?php echo $passage->description; ?>
                                <?php endif; ?>
                                <div class="passage-content" data-id="<?php echo $passage->id; ?>">
                                <?php echo $passage->wrapped; ?>
                                </div>
                                <?php $navbox .= '<button type="button" class="btn btn-sm btn-info btn-navbox" data-index="' . ($index++) . '">P</button>';?>
                            </div>
                            <?php endif; ?>

                            <?php $qlen = count($passage->questions); ?>

                            <?php foreach ($passage->questions as $q => $question) : ?>
                            <div class="slide slide-question" data-index="<?php echo $index; ?>"
                                 data-slide-type="question"
                                 data-section-id="<?php echo $section->id; ?>"
                                 data-id="<?php echo $question->id; ?>"
                                 data-qt-start="true"
                                 data-slide-flag="<?php echo (($q + 1) == $qlen && ($p + 1) == $plen) ? "e" : ''?>">
                                    <div class="row">
                                        <?php if ($passage->qa_layout == 'pqa') : ?>
                                        <div class="col-md-6 col-passage">
                                            <?php echo $passage->wrapped; ?>
                                        </div>
                                        <?php endif; ?>
                                        <div class="<?php echo $passage->qa_layout == 'nqa' ? 'col' : 'col-md-6'?> col-qa">
                                            <div class="question question-content" data-id="<?php echo $question->id; ?>">
                                                <?php echo $question->wrapped; ?>
                                            </div>
                                            <div class="choices">
                                            <?php foreach ($question->choices_computed as $choice) : ?>
                                                <div class="custom-control custom-radio choice"
                                                     data-id="<?php echo $choice->id; ?>"
                                                     data-letter="<?php echo $choice->letter; ?>">
                                                    <input type="radio" id="answer-<?php echo $choice->id; ?>" name="answer[<?php echo $question->id; ?>]" value="<?php echo $choice->id; ?>"
                                                           data-question-id="<?php echo $question->id; ?>"
                                                           class="choice-input custom-control-input">
                                                    <label class="custom-control-label" for="answer-<?php echo $choice->id; ?>">
                                                        <span class="d-flex">
                                                            <?php if (!empty($choice->choice)) : ?>
                                                                <span class="letter pr-1"><?php echo $choice->letter; ?>. </span>
                                                                <span class="flex-fill choice-content" data-id="<?php echo $choice->id; ?>"><?php echo $choice->wrapped; ?></span>
                                                            <?php else : ?>
                                                                <span class="letter pr-1"><?php echo $choice->letter; ?></span>
                                                            <?php endif; ?>
                                                        </span>
                                                    </label>
                                                </div>
                                            <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php $navbox .= '<button type="button" class="btn btn-sm btn-success btn-navbox" data-index="' . ($index++) . '">' . ($qstn++) . '</button>';?>
                            </div>
                            <?php endforeach; ?>

                            <?php endforeach; ?>
                            <?php $navbox .= '</div>' ?>;
                            <?php endforeach; ?>
                        </div>

                        <div class="buttons border-top bg-light p-3 d-flex justify-content-between rounded-bottom">
                            <button type="button" class="btn btn-prev btn-info btn-labeled" disabled>
                                <span class="btn-label"><i class="fas fa-fw fa-arrow-left"></i></span> Prev
                            </button>
                            <button type="button" class="btn btn-next btn-info btn-labeled">
                                Next  <span class="btn-label btn-label-right"><i class="fas fa-fw fa-arrow-right"></i></span>
                            </button>
                            <button type="button" class="btn btn-start btn-info btn-labeled d-none">
                                Start  <span class="btn-label btn-label-right"><i class="fas fa-fw fa-arrow-right"></i></span>
                            </button>
                            <button type="submit" class="btn btn-submit btn-info btn-labeled d-none">
                                Submit  <span class="btn-label btn-label-right"><i class="fas fa-fw fa-upload"></i></span>
                            </button>
                        </div>
                    </div>
                    <div class="col-3 slides-sidebar border-left d-flex flex-column bg-light p-0 rounded-right">
                        @if (settings('debug.show.video') === 1)
                            <div id="sidebar-video">
                                <div class="wrapper position-relative mx-auto">

                                </div>
                            </div>
                        @endif
                        <div id="sidebar-controls" class="sidebar-box p-3 border-bottom text-center">
                            <button type="button" id="timer-toggle" class="btn btn-info btn-sm" data-toggle="collapse" data-target="#sidebar-timer">
                                Hide Timer
                            </button>
                        </div>
                        <div id="sidebar-timer" class="sidebar-box p-3 border-bottom collapse show">
                            <div class="timer text-center text-monospace">
                                <span class="value hr h1">--<span class="unit small">hr</span></span>
                                <span class="value min h1">--<span class="unit small">min</span></span>
                                <span class="value sec h1">--<span class="unit small">sec</span></span>
                            </div>
                        </div>
                        <div id="sidebar-slidenav" class="sidebar-box p-3 flex-fill d-flex flex-column">
                            <h2 class="h4 mt-0 mb-3">Navigation</h2>
                            <div class="flex-fill">
                                <div id="navbox-area">
                                     <div class="navbox-area">
                                        <?php echo $navbox; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="progress-area" class="row h-100 progress-area d-none justify-content-center align-items-center">
                    <div class="col p-3">
                        <p class="h2 font-weight-light text-center">Please wait while submitting your answers to the system.</p>
                        <div class="progress progress-upload w-100">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        @if (!empty($quiz->content_upload))
                        <div class="pt-3">{!! $quiz->content_upload !!}</div>
                        @endif
                        <div id="upload-done" class="d-none text-center mt-3">
                            <button type="button" id="btn-upload-done" class="btn btn-primary">Next</button>
                        </div>
                    </div>
                </div>
                <div id="done-area" class="row h-100 done-area d-none justify-content-center align-items-center">
                    @if (empty($quiz->content_after))
                    <div class="col p-3 text-center">
                        <h2 class="display-4 font-weight-bold mb-4">Thank you!</h2>
                        <p class="h1 font-weight-light mb-5">You answers are submitted to the system. We will get your results from your test giver.</p>
                        <p class="mb-0"><a class="btn btn-lg  btn-outline-primary" href="{{ route('qz.form', ['id' => $quiz->id]) }}">New Quiz</a></p>
                    </div>
                    @else
                    <div class="col p-3">
                        {!! $quiz->content_after !!}
                    </div>
                    @endif

                </div>
            </div>
        </div>
    </form>

    <div id="err-webcam" class="container-fluid d-none">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="alert alert-danger d-none" id="err-support">Please use Chrome or Firefox to take a test.</div>
                <div class="alert alert-danger d-none" id="err-nocam">Your computer does not have webcam. Please have it installed before taking the test.</div>
                <div class="alert alert-danger d-none" id="err-noperm">You didn&apos;t allow your webcam to use this page. Please allow it before taking the test.</div>
                <div class="alert alert-danger d-none" id="err-noread">Tried to access but there is something wrong with your webcam. Please install another one.</div>
                <div class="alert alert-danger d-none" id="err-aborted">For some reason, your webcam is not working. Try restarting the webcam.</div>
                <div class="alert alert-danger d-none" id="err-404">For some reason, I can&apos;t sense the presence of your webcam.</div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-instruction" tabindex="-1" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Calibration</h5>
                </div>
                <div class="modal-body">
                    Please click on each of the 9 points on the screen. You must click on each point 5 times till it goes yellow. This will calibrate your eye movements.
                </div>
                <div class="modal-footer">
                    <button id="btn-calibrate-start" type="button" class="btn btn-primary">Got It!</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-calculate" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Calculating Measurements</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Please don&apos;t move your mouse &amp; stare at the middle dot for the next 5 seconds. This will allow us to calculate the accuracy of our predictions.
                </div>
                <div class="modal-footer">
                    <button id="btn-calculate-start" type="button" class="btn btn-primary">Got It!</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-result" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Result</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Your accuracy is <span id="output-accuracy">&mdash;</span>%.
                </div>
                <div class="modal-footer">
                    <button id="btn-recalibrate" type="button" class="btn btn-primary">Recalibrate</button>
                    <button id="btn-done" type="button" class="btn btn-primary">Got It!</button>
                </div>
            </div>
        </div>
    </div>

    <div class="offline-overlay">
        <p class="h3 font-weight-bold">Connection is interrupted. Attempting to reconnect...</p>
    </div>
@endsection

@section('objects')
<?php unset($quiz->sections);  $quiz->makeHidden(['user', 'urls', 'description']); ?>
<script>window.qm.quiz = @json($quiz); window.qm.testpaper = @json($testpaper); window.qm.sections = @json($sections_array)</script>
<script>window.qm.sid = '<?php echo $session->id; ?>';</script>
<script src="{{ asset('js/webgazer/webgazer.js') }}"></script>
@endsection
