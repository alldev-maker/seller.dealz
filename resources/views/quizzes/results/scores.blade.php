@extends('layout')

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('quizzes.index')  }}">Quizzes Management</a></li>
            <li class="breadcrumb-item"><a href="{{ route('quizzes.results.index')  }}">Results</a></li>
            <li class="breadcrumb-item">ID <?php echo $result->id; ?></li>
            <li class="breadcrumb-item active" aria-current="page">Scores</li>
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
                        <?php foreach ($scores as $score) : ?>
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-header text-center"><?php echo $score->name; ?></div>
                                <div class="card-body">
                                    <div class="display-4 text-center font-weight-bold">
                                        <?php if ($type->slug === 'ssat') : // SSAT ?>
                                        <?php echo $score->score_scaled . ' · ' . $score->score_percent . '%'; ?>
                                        <?php else : ?>
                                        <?php echo $score->score_scaled; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php if ($type->slug === 'sat') : ?>
                            <div class="col-md-3">
                                <div class="card border-primary">
                                    <div class="card-header text-center bg-primary text-white">Overall</div>
                                    <div class="card-body">
                                        <div class="display-4 text-center font-weight-bold">
                                        <?php echo $result->score_scaled; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <!--
                    <div class="row justify-content-center">
                        <div class="col-md-6">
                            <div class="elem-score display-3 font-weight-bold">
                                <div class="d-flex justify-content-center">
                                    <radial-progress-bar v-bind:diameter="300"
                                                         v-bind:start-color="'#50A6FF'"
                                                         v-bind:stop-color="'#50A6FF'"
                                                         v-bind:completed-steps="charts.score.data.percentage"
                                                         v-bind:strokes-linecap="'butt'"
                                                         v-bind:stroke-width="30"
                                                         v-bind:total-steps="100">
                                        <p class="mb-0 display-4 font-weight-bold">@{{ charts.score.data.earned }}</p>
                                    </radial-progress-bar>
                            </div>
                        </div>
                    </div>
                    -->
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script>window.quizmaster.result = { id: '{{ $result->id }}'};</script>
    <script src="{{ asset('js/components/quizzes/results/score.js') }}"></script>
@endsection