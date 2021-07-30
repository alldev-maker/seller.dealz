@extends('layout')

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('quizzes.index')  }}">Quizzes Management</a></li>
            <li class="breadcrumb-item"><a href="{{ route('quizzes.results.index')  }}">Results</a></li>
            <li class="breadcrumb-item">ID <?php echo $result->id; ?></li>
            <li class="breadcrumb-item active" aria-current="page">Answer Key</li>
        </ol>
    </nav>
    <div class="container-fluid px-3">
        <div id="component" class="row">
            <div class="col">
                @component('elements.spinner') @endcomponent
                <div class="content" v-cloak>
                    <h1 class="mt-0 mb-3 text-center">Quiz Results</h1>
                    @component('elements.resultstabs', ['result' => $result]) @endcomponent
                    <div class="row justify-content-center">
                        <div class="col-md-8">
                        <?php foreach ($result->sections as $k => $section) : ?>
                            <table class="answer-strip">
                            <?php foreach ($section->blocks as $i => $blocks) : ?>

                                <?php if ( $i == 1) : ?>
                                <tr class="section-name">
                                    <th class="cell-title" colspan="{{ settings('results.strip.length') + 1 }}">
                                        <?php if ('Section ' . $i === $section->name) : ?>
                                            Section {{ $k + 1 }}
                                        <?php else : ?>
                                            Section {{ $k + 1 }}{{ !empty($section->name) ? ': ' . $section->name : ''}}
                                        <?php endif; ?>
                                    </th>
                                </tr>
                                <?php else : ?>
                                <tr class="section-name">
                                    <th class="cell-title" colspan="{{ settings('results.strip.length') + 1 }}">Section {{ $k + 1 }}: continued...</th>
                                </tr>
                                <?php endif; ?>

                                <tr class="question-no">
                                    <th class="short cell-title">Question No.</th>
                                    <?php $t = count($blocks); $m = 1; ?>
                                    <?php foreach ($blocks as $question) : ; ?>
                                        <td class="cell">{{ $question->number }}</td>
                                        <?php $m++; ?>
                                    <?php endforeach; ?>

                                    <?php  if ( $t < settings('results.strip.length') ) : ?>
                                        <?php for ($m = 1; $m <= settings('results.strip.length') - $t; $m++) : ?>
                                            <td class="cell">&nbsp;</td>
                                        <?php endfor; ?>
                                    <?php endif; ?>
                                </tr>

                                <tr class="correct-answer">
                                    <th class="short cell-title">Correct Answer</th>
                                    <?php $t = count($blocks); $m = 1; ?>
                                    <?php foreach ($blocks as $question) : ; ?>
                                        <td class="cell">{{ $question->correct->letter }}</td>
                                        <?php $m++; ?>
                                    <?php endforeach; ?>

                                    <?php  if ( $t < settings('results.strip.length') ) : ?>
                                        <?php for ($m = 1; $m <= settings('results.strip.length') - $t; $m++) : ?>
                                            <td class="cell">&nbsp;</td>
                                        <?php endfor; ?>
                                    <?php endif; ?>
                                 </tr>

                                <tr class="your-answer">
                                    <th class="short cell-title">Your Answer</th>
                                    <?php $t = count($blocks); $m = 1; ?>
                                    <?php foreach ($blocks as $question) : ?>
                                        <?php if (!empty($question->answer->choice_id)) : ?>
                                            <?php if ($question->correct->letter === $question->answer->letter) : ?>
                                            <td class="cell correct">+</td>
                                            <?php else : ?>
                                            <td class="cell incorrect">{{ $question->answer->letter }}</td>
                                            <?php endif; ?>
                                        <?php else : ?>
                                        <td class="cell skipped">O</td>
                                        <?php endif; ?>
                                        <?php $m++; ?>
                                    <?php endforeach; ?>

                                    <?php  if ( $t < settings('results.strip.length') ) : ?>
                                        <?php for ($m = 1; $m <= settings('results.strip.length') - $t; $m++) : ?>
                                            <td class="cell">&nbsp;</td>
                                        <?php endfor; ?>
                                    <?php endif; ?>
                                </tr>

                                <tr class="difficulty">
                                    <th class="short cell-title">Difficulty Level</th>
                                    <?php $t = count($blocks); $m = 1; ?>
                                    <?php foreach ($blocks as $question) : ; ?>
                                        <td class="cell">{{ $question->difficulty }}</td>
                                    <?php $m++; ?>
                                    <?php endforeach; ?>

                                    <?php  if ( $t < settings('results.strip.length') ) : ?>
                                        <?php for ($m = 1; $m <= settings('results.strip.length') - $t; $m++) : ?>
                                            <td class="cell">&nbsp;</td>
                                        <?php endfor; ?>
                                    <?php endif; ?>
                                </tr>

                                <tr class="explanation">
                                    <th class="short cell-title">Explanation</th>
                                    <?php $t = count($blocks); $m = 1; ?>
                                    <?php foreach ($blocks as $question) : ; ?>
                                    <td class="cell">
                                        @if (!empty($question->explain_video))
                                            <a href="{{ $question->explain_video }}"
                                               target="_blank"
                                               v-b-tooltip.hover
                                               title="Explanation"
                                               v-on:click.prevent="viewMovie('{{ $question->explain_video }}')">
                                                <i class="fas fa-fw fa-"></i>
                                            </a>
                                        @else
                                            &nbsp;
                                        @endif
                                    </td>
                                    <?php $m++; ?>
                                    <?php endforeach; ?>

                                    <?php  if ( $t < settings('results.strip.length') ) : ?>
                                    <?php for ($m = 1; $m <= settings('results.strip.length') - $t; $m++) : ?>
                                    <td class="cell">&nbsp;</td>
                                    <?php endfor; ?>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                            </table>
                        <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Video Modal Start -->
                    <b-modal id="video-modal"
                             no-close-on-backdrop
                             size="lg"
                             v-bind:header-bg-variant="'primary'"
                             v-bind:header-text-variant="'white'"
                             v-on:ok="">
                        <template v-slot:modal-header="{ close }">
                            <h5 class="modal-title" id="entity-passage-form-title">Explanation Video</h5>
                            <button type="button" class="close text-white" v-on:click="close()" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </template>
                        <template v-slot:default>
                            <video width="60%" controls class="d-block mx-auto">
                                <source v-bind:src="current.video">
                            </video>
                        </template>
                        <template v-slot:modal-footer="{ cancel, ok }">
                            <button type="button" class="btn btn-secondary btn-labeled" v-on:click="cancel()">
                                <span class="btn-label"><i class="fas fa-fw fa-times"></i></span> Close
                            </button>
                        </template>
                    </b-modal>
                    <!-- Video Modal End -->
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script>window.quizmaster.result = { id: '{{ $result->id }}'};</script>
    <script src="{{ asset('js/components/quizzes/results/answerkey.js') }}"></script>
@endsection