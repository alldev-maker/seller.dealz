@extends('layout')

@section('content')
    <?php $duration = new \Khill\Duration\Duration(); ?>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('quizzes.index')  }}">Quizzes Management</a></li>
            <li class="breadcrumb-item"><a href="{{ route('quizzes.results.index')  }}">Results</a></li>
            <li class="breadcrumb-item">ID <?php echo $result->id; ?></li>
            <li class="breadcrumb-item active" aria-current="page">Timing</li>
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
                            <?php $total_time = 0; ?>
                            <div id="section-{{ $section->id }}" v-bind:class="{'d-none': selected !== '{{ $section->id }}', 'd-block': selected === '{{ $section->id }}'}">
                                <h3 class="h5 font-weight-bold mt-4 text-center">
                                    <?php if ('Section ' . ($k + 1) === $section->name) : ?>
                                    Section {{ $k + 1 }}
                                    <?php else : ?>
                                    Section {{ $k + 1 }}{{ !empty($section->name) ? ': ' . $section->name : ''}}
                                    <?php endif; ?>
                                </h3>
                                <p>Time Limit: <?php echo $section->time_limit; ?>m</p>
                                <?php foreach ($section->passages as $l => $passage) : ?>
                                    <?php if (!empty($passage->content)) : ?>
                                    <table class="table table-bordered table-passage">
                                        <thead>
                                            <tr>
                                                <th>Title</th>
                                                <th class="text-center">Total Read Time</th>
                                                <th class="text-center">Average</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><?php echo $passage->name; ?></td>
                                                <td class="text-center total"><?php echo $passage->read_time->human; ?></td>
                                                <td class="text-center total"><?php echo $passage->read_avg->human ?? 'N/A'; ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <?php $total_time += $passage->read_time->seconds; ?>
                                    <?php endif; ?>


                                    <table class="table table-bordered table-question">
                                        <thead>
                                            <tr>
                                                <th>Question</th>
                                                <th class="text-center total">Total Read Time</th>
                                                <th class="text-center total">Average</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($passage->questions as $m => $question) : ?>
                                            <tr>
                                                <td><?php echo $question->question; ?></td>
                                                <td class="text-center total"><?php echo $question->read_time->human; ?></td>
                                                <td class="text-center total"><?php echo $question->read_avg->human ?? 'N/A'; ?></td>
                                            </tr>
                                            <?php $total_time += $question->read_time->seconds; ?>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                <?php endforeach; ?>
                                <p>Time: <?php echo $duration->humanize(round($total_time)); ?></p>
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
    <script src="{{ asset('js/components/quizzes/results/timing.js') }}"></script>
@endsection