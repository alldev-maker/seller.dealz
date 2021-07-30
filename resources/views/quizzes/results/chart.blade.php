@extends('layout')

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('quizzes.index')  }}">Quizzes Management</a></li>
            <li class="breadcrumb-item"><a href="{{ route('quizzes.results.index')  }}">Results</a></li>
            <li class="breadcrumb-item">ID <?php echo $result->id; ?></li>
            <li class="breadcrumb-item active" aria-current="page">Chart</li>
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
                        <div class="col">
                            <div v-if="loading" class="text-center p-5">
                                <img class="mb-3" src="{{ url('/images/spinner-loading.svg') }}" width="220" height="10">
                                <div>Loading data...</div>
                            </div>
                            <div id="canvas-holder" class="position-relative">
                                <canvas id="chart" width="600" height="200" style="width: 100%"></canvas>
                                <!-- <apexchart type="line" height="600" v-bind:options="chart.options" v-bind:series="chart.series"></apexchart> -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script>window.quizmaster.result = { id: '{{ $result->id }}'};</script>
    <script src="{{ asset('js/components/quizzes/results/chart.js') }}"></script>
@endsection