@extends('layout')

@section('content')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item active" aria-current="page">Settings</li>
    </ol>
</nav>
<div class="container-fluid px-3">
    <div id="settings" class="row">
        <div class="col">
            @component('elements.spinner') @endcomponent
            <div class="content" v-cloak>
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <form name="settings" data-vv-scope="settings">
                            <b-tabs content-class="mt-3" align="center">
                                <b-tab title="System Settings">
                                    <p>I'm the first tab</p>
                                </b-tab>
                                <b-tab title="Results">
                                    <p>I'm the first tab</p>
                                </b-tab>
                                <b-tab title="Media Gallery">
                                    <p>I'm the first tab</p>
                                </b-tab>
                                <b-tab title="Debugging">

                                </b-tab>
                            </b-tabs>
                            <p class="m-0 text-center">
                                <button type="submit" class="btn btn-lg btn-primary btn-labeled" v-bind:disabled="errors.any('component')">
                                    <span class="btn-label">
                                        <i v-bind:class="{ 'fa-cog fa-spin': submitting, 'fa-upload': !submitting }" class="fas fa-fw"></i></span> Submit
                                </button>
                            </p>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script>window.quizmaster.message = {!! json_encode($message) !!}; </script>
<script src="{{ asset('js/components/admin/settings/index.js') }}"></script>
@endsection