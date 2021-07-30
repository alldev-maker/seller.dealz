@extends('layout')

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('quizzes.index')  }}">Quizzes Management</a></li>
            <li class="breadcrumb-item"><a href="{{ route('quizzes.quizzes.index')  }}">Quizzes</a></li>
            <li class="breadcrumb-item"><?php echo $quiz->name; ?></li>
            <li class="breadcrumb-item active" aria-current="page">Settings</li>
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
                                    <div class="card-header"><h2 class="h6 m-0 font-weight-bold">Settings</h2></div>
                                    <div class="card-body">
                                        <div class="form-group form-row">
                                            <label for="enable" class="col-md-3 col-form-label">Enable</label>
                                            <div class="col">
                                                <b-form-checkbox v-model="quiz.enabled"
                                                                 size="lg"
                                                                 switch
                                                                 v-on:change="doDepcheck()"
                                                                 >
                                                </b-form-checkbox>
                                                <div class="form-text small text-muted">Enable the quiz and make it visible to the web.</div>
                                            </div>
                                        </div>
                                        <!--
                                        <div class="form-group form-row">
                                            <label for="name" class="col-md-3 col-form-label">Enable Until</label>
                                            <div class="col">

                                                <div class="form-text small text-muted">Set the deadline of the quiz.</div>
                                            </div>
                                        </div>
                                        -->
                                        <div class="form-group form-row">
                                            <label for="name" class="col-md-3 col-form-label">Time Limit</label>
                                            <div class="col">
                                                <div class="input-group col-md-6 pl-0">
                                                    <input type="number" class="form-control" v-model="quiz.duration" min="0">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">minutes</span>
                                                    </div>
                                                </div>
                                                <div class="form-text small text-muted">Set the duration of the quiz. Set the value to zero for no limit.</div>
                                            </div>
                                        </div>
                                        <div class="form-group form-row">
                                            <label for="name" class="col-md-3 col-form-label">Autosubmit</label>
                                            <div class="col">
                                                <b-form-checkbox v-model="quiz.auto_submit"
                                                                 size="lg"
                                                                 switch
                                                                 v-bind:disabled="quiz.duration === 0"
                                                >
                                                </b-form-checkbox>
                                                <div class="form-text small text-muted">Submit the form automatically when the time is up.</div>
                                            </div>
                                        </div>
                                        <div class="form-group form-row">
                                            <label for="name" class="col-md-3 col-form-label">Shuffle Passages</label>
                                            <div class="col">
                                                <b-form-checkbox v-model="quiz.shuffle_passages" size="lg" switch></b-form-checkbox>
                                                <div class="form-text small text-muted">Make the passages for reading comprehension in random order.</div>
                                            </div>
                                        </div>
                                        <div class="form-group form-row">
                                            <label for="name" class="col-md-3 col-form-label">Shuffle Questions</label>
                                            <div class="col">
                                                <b-form-checkbox v-model="quiz.shuffle_questions" size="lg" switch></b-form-checkbox>
                                                <div class="form-text small text-muted">Make the questions in the section in random order.</div>
                                            </div>
                                        </div>
                                        <div class="form-group form-row">
                                            <label for="name" class="col-md-3 col-form-label">Shuffle Choices</label>
                                            <div class="col">
                                                <b-form-checkbox v-model="quiz.shuffle_choices" size="lg" switch></b-form-checkbox>
                                                <div class="form-text small text-muted">Make the choices in multiple-choice question in random order.</div>
                                            </div>
                                        </div>
                                        <div class="form-group form-row">
                                            <label for="name" class="col-md-3 col-form-label">Allow Guests</label>
                                            <div class="col">
                                                <b-form-checkbox v-model="quiz.allow_guests" size="lg" switch></b-form-checkbox>
                                                <div class="form-text small text-muted">Allow non-registered test takers to take the test.</div>
                                            </div>
                                        </div>
                                        <div class="form-group form-row">
                                            <label for="name" class="col-md-3 col-form-label">Multiple Takes</label>
                                            <div class="col">
                                                <b-form-checkbox v-model="quiz.multiple_takes" size="lg" switch></b-form-checkbox>
                                                <div class="form-text small text-muted">Allow test takers to take the quiz multiple times.</div>
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
                                    <span class="btn-label">
                                        <i v-bind:class="{ 'fa-cog fa-spin': submitting, 'fa-upload': !submitting }" class="fas fa-fw"></i></span> Submit
                                    </button>
                                    <a class="btn btn-lg btn-secondary btn-labeled" href="<?php echo route('quizzes.quizzes.index'); ?>">
                                        <span class="btn-label"><i v-bind:class="{ 'fa-cog fa-spin': submitting, 'fa-chevron-left': !submitting }" class="fas fa-fw"></i></span> Go Back
                                    </a>
                                </p>
                            </div>
                        </div>
                    </form>

                    <!-- Depcheck (Deployment Check) Modal Start -->
                    <b-modal id="depcheck-modal"
                             no-close-on-backdrop
                             title="Deployment Check"
                             v-bind:header-bg-variant="'info'"
                             v-bind:header-text-variant="'white'">
                        <template v-slot:modal-title>
                            Deployment Check
                        </template>
                        <template v-slot:default>
                            <div v-if="depcheck.checking">
                                Please wait while the system is checking this Quiz for deployment.
                            </div>
                            <div v-if="!depcheck.checking && depcheck.result.flag">
                                <span class="text-success font-weight-bold">@{{ depcheck.result.message }}</span>
                            </div>
                            <div v-if="!depcheck.checking && !depcheck.result.flag">
                                <p class="text-danger font-weight-bold">@{{ depcheck.result.message }}</p>
                                <div v-for="reason in depcheck.result.reason">
                                    <p>@{{ reason.message }}</p>
                                    <ol v-if="reason.questions">
                                        <li v-for="question in reason.questions" v-bind:data-qid="question.id">
                                            @{{ question.name }}<br>
                                            <small class="text-muted">Section: @{{ question.section.name }}</small><br>
                                            <small class="text-muted">Passage: @{{ question.passage.name }}</small>
                                        </li>
                                    </ol>
                                </div>
                            </div>
                        </template>
                        <template v-slot:modal-footer="{ cancel, ok }">
                            <button type="submit" class="btn btn-success btn-labeled" v-on:click="ok()" v-bind:class="{ 'disabled': depcheck.checking }">
                                <span class="btn-label"><i v-bind:class="{ 'fa-cog fa-spin': depcheck.checking, 'fa-check': !depcheck.checking }" class="fas fa-fw fa-check"></i></span> Okay
                            </button>
                        </template>
                    </b-modal>
                    <!-- Depcheck (Deployment Check) Modal End -->
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script>Quiz = {!! $quiz->toJson() !!};</script>
    <script src="{{ asset('js/components/quizzes/quizzes/settings.js') }}"></script>
@endsection