@extends('layout')

@section('title')
    Quizzes Management :: {{ settings('site.title') }}
@endsection

@section('content')
    <div class="component">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item active">Quizzes Management</li>
            </ol>
        </nav>
        <div class="container-fluid">
            <div id="component" class="row">
                <div class="col">
                    <div class="content pb-3">
                        <div class="row justify-content-center">
                            <div class="col-md-2">
                                <a class="card card-icon text-center" href="{{ route('quizzes.quizzes.index') }}">
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <i class="fas fa-fw fa-4x fa-feather"></i>
                                        </div>
                                        <h3 class="h6 card-title mb-0">Quizzes</h3>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-2">
                                <a class="card card-icon text-center" href="{{ route('quizzes.results.index') }}">
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <i class="fas fa-fw fa-4x fa-poll"></i>
                                        </div>
                                        <h3 class="h6 card-title mb-0">Results</h3>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-2">
                                <a class="card card-icon text-center" href="{{ route('quizzes.types.sections.index') }}">
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <i class="fas fa-fw fa-4x fa-tags"></i>
                                        </div>
                                        <h3 class="h6 card-title mb-0">Section Types</h3>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-2">
                                <a class="card card-icon text-center" href="{{ route('quizzes.types.problems.index') }}">
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <i class="fas fa-fw fa-4x fa-tags"></i>
                                        </div>
                                        <h3 class="h6 card-title mb-0">Problem Types</h3>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection