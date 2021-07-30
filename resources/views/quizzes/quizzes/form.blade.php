@extends('layout')

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('quizzes.index')  }}">Quizzes Management</a></li>
            <li class="breadcrumb-item"><a href="{{ route('quizzes.quizzes.index')  }}">Quizzes</a></li>
            <li class="breadcrumb-item"><?php echo $quiz->name; ?></li>
            <li class="breadcrumb-item active" aria-current="page">About</li>
        </ol>
    </nav>
    <div class="container-fluid px-3">
        <div id="component" class="row">
            <div class="col">
                @component('elements.spinner') @endcomponent
                <div class="content" v-cloak>
                    @component('elements.quiztabs', ['quiz' => $quiz]) @endcomponent
                    <form v-on:submit.prevent="submit" data-vv-scope="quiz">
                        <div class="row justify-content-center">
                            <div class="col-md-8">
                                <div class="card mb-3">
                                    <div class="card-header"><h2 class="h6 m-0 font-weight-bold">General Information</h2></div>
                                    <div class="card-body">
                                        <div class="form-group form-row">
                                            <label for="name" class="col-md-3 col-form-label">Quiz Name <sup class="text-danger"><i class="fas fa-sm fa-asterisk"></i></sup></label>
                                            <div class="col">
                                                <input type="text" id="name" name="name" class="form-control"
                                                       v-model="quiz.name"
                                                       v-validate="'required'"
                                                       v-bind:class="{ 'is-invalid': errors.has('quiz.name') }"
                                                >
                                                <div v-for="error in errors.collect('quiz.name')" class="invalid-feedback">@{{ error }}</div>
                                                <div class="form-text small text-muted">Name of the quiz or test.</div>
                                            </div>
                                        </div>
                                        <div class="form-group form-row">
                                            <label for="name" class="col-md-3 col-form-label">Description</label>
                                            <div class="col">
                                                <editor v-model="quiz.description" v-bind:init="config.tinymce"></editor>
                                                <div class="form-text small text-muted">The description of the quiz or test.</div>
                                            </div>
                                        </div>
                                        @role(['admin', 'developer'])
                                        <div class="form-group form-row">
                                            <label for="role" class="col-md-3 col-form-label">Scoring Type <sup class="text-danger"><i class="fas fa-sm fa-asterisk"></i></sup></label>
                                            <div class="col">
                                                <v-select label="name" v-model="quiz.scoring_type" v-bind:clearable="false" v-bind:options="scoring_types.items" v-validate="'required'"></v-select>
                                                <div v-for="error in errors.collect('quiz.scoring_type')" class="invalid-feedback">@{{ error }}</div>
                                            </div>
                                        </div>
                                        <div class="form-group form-row">
                                            <label for="name" class="col-md-3 col-form-label">Owner <sup class="text-danger"><i class="fas fa-sm fa-asterisk"></i></sup></label>
                                            <div class="col">
                                                <v-select label="nice_name" v-model="quiz.user" v-bind:clearable="false" v-bind:options="users.items" v-validate="'required'"></v-select>
                                                <div class="form-text small text-muted">The owner of the Quiz.</div>
                                            </div>
                                        </div>
                                        @endrole
                                        <div class="form-group">
                                            <label for="content_after">Content During Upload</label>
                                            <div>
                                                <editor v-model="quiz.content_upload" v-bind:init="config.large"></editor>
                                                <div class="form-text small text-muted">The content upon submission and during the upload.</div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="content_after">Content After Upload</label>
                                            <div>
                                                <editor v-model="quiz.content_after" v-bind:init="config.large"></editor>
                                                <div class="form-text small text-muted">The content after the uploading the answers.</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <p class="m-0 text-center">
                                    <button type="submit" class="btn btn-lg btn-primary btn-labeled" v-bind:disabled="errors.any('component')">
                                    <span class="btn-label"><i v-bind:class="{ 'fa-cog fa-spin': submitting, 'fa-upload': !submitting }" class="fas fa-fw"></i></span> Submit
                                    </button>
                                    <a class="btn btn-lg btn-secondary btn-labeled" href="<?php echo route('quizzes.quizzes.index'); ?>">
                                        <span class="btn-label"><i v-bind:class="{ 'fa-cog fa-spin': submitting, 'fa-chevron-left': !submitting }" class="fas fa-fw"></i></span> Go Back
                                    </a>
                                </p>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script>window.quizmaster.quiz = @json($quiz)</script>
    <script src="{{ asset('js/tinymce/tinymce.js') }}"></script>
    <script src="{{ asset('js/components/quizzes/quizzes/form.js') }}"></script>
@endsection