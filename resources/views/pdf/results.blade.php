@extends('layout-pdf')

@section('title', 'Quiz Result Report')

@section('content')
    <div class="container-fluid position-relative">
        <div class="row justify-content-center">
            <div class="col">
                <h1 class="mt-0 mb-3 text-center">Quiz Results</h1>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="card mb-3">
                    <div class="card-header"><h2 class="h6 m-0 font-weight-bold">General Information</h2></div>
                    <div class="card-body">
                        <dl class="row m-0">
                            <dt class="col-4">Result ID</dt>
                            <dd class="col-8">{{ $result->id }}</dd>
                            <dt class="col-4">Quiz Name</dt>
                            <dd class="col-8">{{ $result->quiz_name }}</dd>
                            <dt class="col-4">Test Taker</dt>
                            <dd class="col-8">{{ $result->test_taker_name }}</dd>
                            <dt class="col-4">Email</dt>
                            <dd class="col-8">{{ $result->email }}</dd>
                            <dt class="col-4">Date Taken</dt>
                            <dd class="col-8">{{ $result->time_start->format('F j, Y g:i:s a, T') }}</dd>
                            <dt class="col-4">Date Submitted</dt>
                            <dd class="col-8">{{ $result->time_end->format('F j, Y g:i:s a, T') }}</dd>
                            <dt class="col-4">Test Duration</dt>
                            <dd class="col-8">{{ $result->duration }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-6">
                <div class="card mb-3">
                    <div class="card-header"><h2 class="h6 m-0 font-weight-bold">Tests Information</h2></div>
                    <div class="card-body">
                        <dl class="row">
                            <dt class="col-4">Score</dt>
                            <dd class="col-8">{{ $result->score }} / {{ $result->total }}</dd>
                        </dl>
                        <p class="font-weight-bold">Questions</p>
                        <dl class="row ml-3 mb-0">
                            <dt class="col-4">Total</dt>
                            <dd class="col-8">{{ $result->questions_count }}</dd>
                            <dt class="col-4">Answered</dt>
                            <dd class="col-8">{{ $result->questions_answered }}</dd>
                            <dt class="col-4">Skipped</dt>
                            <dd class="col-8">{{ $result->questions_skipped }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card mb-3">
                    <div class="card-header"><h2 class="h6 m-0 font-weight-bold">Biometric Information</h2></div>
                    <div class="card-body">
                        <dl class="row mb-0">
                            <dt class="col-4">Calibration Accuracy</dt>
                            <dd class="col-8">{{ $result->calibration }}%</dd>
                            <dt class="col-4">Pulse Rate</dt>
                            <dd class="col-8">{{ number_format($result->avg_pulse, 2) }} bpm</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col">

            </div>
        </div>
    </div>
@endsection