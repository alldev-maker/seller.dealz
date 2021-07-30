@extends('layout')

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('quizzes.index')  }}">Quizzes Management</a></li>
            <li class="breadcrumb-item"><a href="{{ route('quizzes.results.index')  }}">Results</a></li>
            <li class="breadcrumb-item">ID <?php echo $result->id; ?></li>
            <li class="breadcrumb-item active" aria-current="page">Eye Tracking</li>
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
                            <div class="selection text-center mb-4 w-50 mx-auto">
                                <p class="mb-1">Please select the section</p>
                                <select class="form-control" v-model="selected">
                                    <?php foreach ($result->sections as $k => $section) : ?>
                                    <option value="{{ $section->id }}">{{ $section->name }}</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php foreach ($result->sections as $k => $section) : ?>
                            <div id="section-{{ $section->id }}" v-bind:class="{'d-none': selected !== '{{ $section->id }}', 'd-block': selected === '{{ $section->id }}'}">
                                <h3 class="h5 font-weight-bold text-center">
                                    <?php if ('Section ' . ($k + 1) === $section->name) : ?>
                                    Section {{ $k + 1 }}
                                    <?php else : ?>
                                    Section {{ $k + 1 }}{{ !empty($section->name) ? ': ' . $section->name : ''}}
                                    <?php endif; ?>
                                </h3>
                                <?php foreach ($section->passages as $l => $passage) : ?>
                                    <?php if (!empty($passage->content)) : ?>
                                    <table class="table table-bordered table-passage">
                                        <tbody>
                                            <tr>
                                                <th width="20%">Title</th>
                                                <td><?php echo $passage->name; ?></td>
                                            </tr>
                                            <tr>
                                                <th width="20%">Content</th>
                                                <td class="content"><?php echo $passage->content; ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <?php endif; ?>

                                    <?php foreach ($passage->questions as $m => $question) : ?>
                                    <table class="table table-bordered table-question">
                                        <tbody>
                                            <tr>
                                                <th width="20%">Question</th>
                                                <td class="question" colspan="4"><?php echo $question->question; ?></td>
                                            </tr>
                                            <?php if ($passage->content != '') : ?>
                                            <tr>
                                                <th width="20%">Passage Reread</th>
                                                <td class="question" colspan="4"><?php echo $question->passage_count > 0 ? 'Yes' : 'No'; ?></td>
                                            </tr>
                                            <?php endif; ?>
                                            <?php foreach ($question->choices as $n => $choice) : ?>
                                            <tr>
                                                <?php if ($n == 0) : ?>
                                                <th width="20%" rowspan="<?php echo count($question->choices); ?>">Choices</th>
                                                <?php endif; ?>
                                                <td class="letter text-center">
                                                    <?php echo $choice->letter; ?>
                                                </td>
                                                <td class="choice">
                                                    <?php echo $choice->choice_name; ?>
                                                </td>
                                                <td class="letter text-center">
                                                    <?php if ($question->answer->letter == $choice->letter) : ?>
                                                    <i class="fa fa-lg {{  $question->answer->letter == $question->correct->letter ? 'fa-check text-success' : 'fa-times text-danger' }}"></i>
                                                    <?php else: ?>
                                                    &nbsp;
                                                    <?php endif; ?>
                                                </td>
                                                <td class="letter text-center">
                                                    <?php if ($question->correct->letter == $choice->letter) : ?>
                                                    <i class="fa fa-circle fa-lg text-success"></i>
                                                    <?php else: ?>
                                                    &nbsp;
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script>window.quizmaster.result = { id: '{{ $result->id }}' }; window.quizmaster.selected = '{{ $result->sections[0]->id }}';</script>
    <script src="{{ asset('js/components/quizzes/results/eyetracking.js') }}"></script>
@endsection