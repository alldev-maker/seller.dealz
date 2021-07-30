@extends('layout-preview')

@section('title')
{{ $quiz->name }} Preview {{ ts() }} Quizzes {{ ts() }} Quizzes Management {{ ts() }} {{ settings('site.title') }}
@endsection


@section('content')
<form id="quiz" class="h-100 d-flex justify-content-center align-items-center" name="quiz">
    <div class="border rounded quiz-area w-90 h-90">
        <div class="container-fluid h-100">
            <div id="slides-area" class="row h-100 slides-area">
                <div class="col-9 slides-panel rounded-left d-flex flex-column p-0">
                    <?php $navbox = ''; $index = 0; $qstn = 1; ?>
                    <div class="slides w-100 flex-fill">
                        <div class="slide slide-title slide-quiz-name d-flex justify-content-center align-items-center"
                             data-index="<?php echo $index; ?>"
                             data-slide-type="home"
                             data-id="0">
                            <h1 class="mb-0 display-3 font-weight-bold text-center"><?php echo $quiz->name; ?></h1>
                            <?php $navbox .= '<button type="button" class="btn btn-sm btn-primary btn-navbox active" data-index="' . ($index++) . '"><i class="fas fa-fw fa-home"></i></button>';?>
                        </div>

                        <div class="slide slide-details d-flex justify-content-center align-items-center"
                             data-index="<?php echo $index; ?>"
                             data-slide-type="form"
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
                            <?php $navbox .= '<button type="button" class="btn btn-sm btn-secondary btn-navbox" data-index="' . ($index++) . '">F</button>';?>
                        </div>

                        <div class="slide slide-details d-flex justify-content-center align-items-center"
                             data-index="<?php echo $index; ?>"
                             data-slide-type="form"
                             data-id="0">
                            <div class="details w-75">
                                <h2 class="display-4 text-center">Your target score?</h2>
                                <div class="text-center mt-3">
                                    <input type="number" class="form-control form-control-lg text-center w-50 mx-auto" id="target_score" name="target_score" min="70" max="100" >
                                </div>
                            </div>
                            <?php $navbox .= '<button type="button" class="btn btn-sm btn-secondary btn-navbox" data-index="' . ($index++) . '">F</button>';?>
                        </div>

                        <?php if (!empty($quiz->description)) : ?>
                        <div class="slide slide-description slide-quiz-description"
                             data-index="<?php echo $index; ?>"
                             data-slide-type="quiz-description"
                             data-id="0">
                            <?php echo $quiz->description; ?>
                            <?php $navbox .= '<button type="button" class="btn btn-sm btn-secondary btn-navbox" data-index="' . ($index++) . '">&nbsp;</button>';?>
                        </div>
                        <?php endif; ?>

                        <?php foreach ($quiz->sections as $section) : ?>

                        <?php if (count($quiz->sections) > 1) : ?>
                        <div class="slide slide-section slide-section-name d-flex justify-content-center align-items-center"
                             data-index="<?php echo $index; ?>"
                             data-slide-type="section"
                             data-id="<?php echo $section->id; ?>"
                             data-qt-start>
                            <h2 class="mb-0 display-4 font-weight-bold text-center"><?php echo $section->name; ?></h2>
                            <?php $navbox .= '<button type="button" class="btn btn-sm btn-secondary btn-navbox" data-index="' . ($index++) . '">S</button>';?>
                        </div>

                        <?php if (!empty($section->description)) : ?>
                        <div class="slide slide-section slide-section-description" data-index="<?php echo $index; ?>"
                             data-slide-type="section-description"
                             data-id="<?php echo $section->id; ?>"
                             data-qt-start>
                            <?php echo $section->description; ?>
                            <?php $navbox .= '<button type="button" class="btn btn-sm btn-secondary btn-navbox"  data-index="' . ($index++) . '">&nbsp;</button>';?>
                        </div>
                        <?php endif; ?>
                        <?php endif; ?>

                        <?php foreach ($section->passages as $passage) : ?>

                        <?php if (!empty($passage->content)) : ?>
                        <div class="slide slide-passage" data-index="<?php echo $index; ?>"
                             data-slide-type="passage"
                             data-id="<?php echo $passage->id; ?>"
                             data-qt-start>
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

                        <?php foreach ($passage->questions as $question) : ?>
                        <div class="slide slide-question" data-index="<?php echo $index; ?>"
                             data-slide-type="question"
                             data-id="<?php echo $question->id; ?>"
                             data-qt-start>
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
                                    <!-- TO DO: Improve semantics -->
                                        <div class="custom-control custom-radio choice"
                                             data-id="<?php echo $choice->id; ?>"
                                             data-letter="<?php echo $choice->letter; ?>">
                                            <input type="radio" id="answer-<?php echo $choice->id; ?>" name="answer[<?php echo $question->id; ?>]" value="<?php echo $choice->id; ?>"
                                                   data-question-id="<?php echo $question->id; ?>"
                                                   class="choice-input custom-control-input">
                                            <label class="custom-control-label" for="answer-<?php echo $choice->id; ?>">
                                        <span class="d-flex">
                                            <p class="letter pr-1"><?php echo $choice->letter; ?>. </p>
                                            <div class="flex-fill choice-content" data-id="<?php echo $choice->id; ?>"><?php echo $choice->wrapped; ?></div>
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

                        <?php endforeach; ?>
                    </div>

                    <div class="buttons border-top bg-light p-3 d-flex justify-content-between rounded-bottom">
                        <button type="button" class="btn btn-prev btn-info btn-labeled" disabled>
                            <span class="btn-label"><i class="fas fa-fw fa-arrow-left"></i></span> Prev
                        </button>
                        <button type="button" class="btn btn-next btn-info btn-labeled">
                            Next  <span class="btn-label btn-label-right"><i class="fas fa-fw fa-arrow-right"></i></span>
                        </button>
                        <button type="button" class="btn btn-submit btn-info btn-labeled d-none">
                            Submit  <span class="btn-label btn-label-right"><i class="fas fa-fw fa-upload"></i></span>
                        </button>
                    </div>
                </div>
                <div class="col-3 slides-sidebar border-left d-flex flex-column bg-light p-0 rounded-right">
                    @if (settings('debug.show.video') === 1)
                        <div id="sidebar-video" class="bg-light-dark" style="height: 240px"></div>
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
                                 <div class="navbox-area">
                                    <?php echo $navbox; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@section('javascript')
<script>window.quizmaster.quiz = @json($quiz); </script>
<script src="{{ asset('js/components/quizzes/quizzes/view.js') }}"></script>
@endsection
