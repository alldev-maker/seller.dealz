@extends('layout-frontend-test')

@section('title', $quiz->name)

@section('stylesheet', asset('/css/frontend.css'))

@section('content')
    <form id="quiz" class="h-100 d-flex justify-content-center align-items-center" name="quiz">
        <div class="border rounded quiz-area w-90 h-90">
            <div class="container-fluid h-100">
                <div id="slides-area" class="row h-100 slides-area">
                    <div class="col-9 slides-panel rounded-left d-flex flex-column p-0">
                        <div class="slides w-100 flex-fill">
                            <div class="slide slide-title slide-quiz-name d-flex justify-content-center align-items-center"
                                 data-index="0"
                                 data-slide-type="home"
                                 data-slide-group="init"
                                 data-id="0">
                                <h1 class="mb-0 display-3 font-weight-bold text-center"><?php echo $quiz->name; ?></h1>
                            </div>

                            <div class="slide slide-details d-flex justify-content-center align-items-center"
                                 data-index="1"
                                 data-slide-type="form"
                                 data-slide-group="init"
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
                            </div>

                            <div class="slide slide-details d-flex justify-content-center align-items-center"
                                 data-index="2"
                                 data-slide-type="form"
                                 data-slide-group="init"
                                 data-id="0">
                                <div class="details w-75">
                                    <h2 class="display-4 text-center">Your target score?</h2>
                                    <div class="text-center mt-3">
                                        <input type="number" class="form-control form-control-lg text-center w-50 mx-auto" id="target_score" name="target_score" min="70" max="100" >
                                    </div>
                                </div>
                            </div>

                            <?php if (!empty($quiz->description)) : ?>
                            <div class="slide slide-description slide-quiz-description"
                                 data-index="3"
                                 data-slide-group="init"
                                 data-slide-type="quiz-description"
                                 data-id="0">
                                <?php echo $quiz->description; ?>
                            </div>
                            <?php endif; ?>
                        </div>

                        <div class="buttons border-top bg-light p-3 d-flex justify-content-between rounded-bottom">
                            <button type="button" class="btn btn-prev btn-info btn-labeled" disabled>
                                <span class="btn-label"><i class="fas fa-fw fa-arrow-left"></i></span> Prev
                            </button>
                            <button type="button" class="btn btn-next btn-info btn-labeled">
                                Next  <span class="btn-label btn-label-right"><i class="fas fa-fw fa-arrow-right"></i></span>
                            </button>
                            <button type="button" class="btn btn-proceed btn-info btn-labeled d-none">
                                Proceed  <span class="btn-label btn-label-right"><i class="fas fa-fw fa-arrow-right"></i></span>
                            </button>
                            <button type="submit" class="btn btn-submit btn-info btn-labeled d-none">
                                Submit  <span class="btn-label btn-label-right"><i class="fas fa-fw fa-upload"></i></span>
                            </button>
                        </div>
                    </div>
                    <div class="col-3 slides-sidebar border-left d-flex flex-column bg-light p-0 rounded-right">
                        @if (settings('debug.show.video') === 1)
                            <div id="sidebar-video">
                                <div class="wrapper position-relative mx-auto"> </div>
                            </div>
                        @endif
                        @if ($quiz->duration > 0)
                        <div id="sidebar-controls" class="sidebar-box p-3 border-bottom text-center">
                            <button type="button" id="timer-toggle" class="btn btn-info btn-sm" data-toggle="collapse" data-target="#sidebar-timer">
                                Hide Timer
                            </button>
                        </div>
                        <div id="sidebar-timer" class="sidebar-box p-3 border-bottom collapse show">
                            <div class="timer text-center text-monospace">
                                <?php
                                    $hours = floor($quiz->duration / 60);
                                    $minutes = floor($quiz->duration - ($hours * 60));
                                ?>
                                <?php if ($quiz->duration > 59) : ?>
                                <span class="value hr h1"><?php echo str_pad($hours, 2,'0', STR_PAD_LEFT); ?><span class="unit small">hr</span></span>
                                <?php endif; ?>
                                <span class="value min h1"><?php echo str_pad($minutes, 2,'0', STR_PAD_LEFT); ?><span class="unit small">min</span></span>
                                <span class="value sec h1">00<span class="unit small">sec</span></span>
                            </div>
                        </div>
                        @endif
                        <div id="sidebar-slidenav" class="sidebar-box p-3 flex-fill d-flex flex-column">
                            <h2 class="h4 mt-0 mb-3">Navigation</h2>
                            <div class="flex-fill">
                                <div id="navbox-area">
                                     <div class="navbox-area"></div>
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

    <div class="offline-overlay">
        <p class="h3 font-weight-bold">Connection is interrupted. Attempting to reconnect...</p>
    </div>
@endsection

@section('objects')
<?php unset($quiz->sections);  $quiz->makeHidden(['user', 'urls', 'description']); ?>
<script>window.qm.quiz = @json($quiz); window.qm.testpaper = @json($testpaper);</script>
<script>window.qm.sid = '<?php echo $session->id; ?>';</script>
@endsection
