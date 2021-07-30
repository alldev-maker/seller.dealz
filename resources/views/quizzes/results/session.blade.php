@extends('layout')

@section('stylesheet')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/rrweb-player@latest/dist/style.css" />
@endsection

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('quizzes.index')  }}">Quizzes Management</a></li>
            <li class="breadcrumb-item"><a href="{{ route('quizzes.results.index')  }}">Results</a></li>
            <li class="breadcrumb-item">ID <?php echo $result->id; ?></li>
            <li class="breadcrumb-item active" aria-current="page">Session</li>
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
                            <div v-if="loading" class="text-center p-5">
                                <img class="mb-3" src="{{ url('/images/spinner-loading.svg') }}" width="220" height="10">
                                <div>Loading data...</div>
                            </div>
                            <div id="session-player-area"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script>Result = { id: '{{ $result->id }}'};</script>
    <script src="https://cdn.jsdelivr.net/npm/rrweb@latest/dist/record/rrweb-record.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/rrweb-player@latest/dist/index.js"></script>
    <script src="{{ asset('js/components/quizzes/results/session.js') }}"></script>
@endsection