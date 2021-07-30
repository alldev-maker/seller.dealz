@extends('layout')

@section('content')
    <div class="component">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Roster</li>
            </ol>
        </nav>
        <div class="container-fluid">
            <div id="component" class="row">
                <div class="col">
                    @component('elements.spinner') @endcomponent
                    <div class="content pb-3" v-cloak>
                        <div class="row justify-content-center">
                            <div class="col-md-2">
                                <a class="card card-icon text-center" href="{{ route('admin.users.index') }}">
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <i class="fas fa-fw fa-4x fa-user"></i>
                                        </div>
                                        <h3 class="h6 card-title mb-0">Users</h3>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-2">
                                <a class="card card-icon text-center" href="{{ route('admin.users.index') }}">
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <i class="fas fa-fw fa-4x fa-users"></i>
                                        </div>
                                        <h3 class="h6 card-title mb-0">Groups</h3>
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

@section('javascript')
    <script src="{{ route('admin.index.index.js') }}"></script>
@endsection