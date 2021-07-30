@extends('layout')

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('quizzes.index')  }}">Quizzes Management</a></li>
            <li class="breadcrumb-item"><a href="{{ route('quizzes.quizzes.index')  }}">Quizzes</a></li>
            <li class="breadcrumb-item"><?php echo $quiz->name; ?></li>
            <li class="breadcrumb-item active" aria-current="page">Questionnaire</li>
        </ol>
    </nav>
    <div class="container-fluid px-3">
        <div id="component" class="row">
            <div class="col">
                @component('elements.spinner') @endcomponent
                <div class="content" v-cloak>
                    @component('elements.quiztabs', ['quiz' => $quiz]) @endcomponent
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-header"><h4 class="h6 font-weight-bold mb-0">Sections</h4></div>
                                <draggable tag="ul"
                                           class="list-group list-group-hover list-group-flush"
                                           v-bind:draggable="'.element'"
                                           v-bind:handle="'.btn-handle'"
                                           v-on:update="sortSections">
                                    <li v-if="loading.sections" class="list-group-item">
                                        @component('elements.progressbars.thin') @endcomponent
                                    </li>
                                    <li v-if="quiz.sections.length === 0 && !loading.sections" class="list-group-item">
                                        No sections in the quiz. Add a new section.
                                    </li>
                                    <li v-if="quiz.sections.length > 0 && !loading.sections" v-for="section in quiz.sections"
                                        class="list-group-item element cursor-pointer list-group-item-action"
                                        v-bind:class="{ 'list-group-item-primary': section.id == current.section.id }"
                                        v-on:click="selectSection(section)"
                                        v-bind:key="section.name"
                                    >
                                        <div class="d-flex align-items-center">
                                            <span class="handle mr-2">
                                                <button type="button" class="btn-handle btn btn-xs btn-outline-dark" v-b-tooltip.hover title="Drag the handle to sort">
                                                    <i class="fas fa-fw fa-sm fa-arrows-alt-v"></i>
                                                </button>
                                            </span>
                                            <span class="name flex-fill mr-2">@{{ section.name }}</span>
                                            <span class="buttons text-right">
                                                <button type="button" class="btn btn-xs btn-info" v-b-tooltip.hover title="Edit" v-on:click="openSectionFormModal(section)">
                                                    <i class="fas fa-fw fa-sm fa-pencil-alt"></i>
                                                </button>
                                                <button type="button" class="btn btn-xs btn-danger" v-b-tooltip.hover title="Delete" v-on:click="openConfirmDeleteSectionModal(section)">
                                                    <i class="fas fa-fw fa-sm fa-times"></i>
                                                </button>
                                            </span>
                                        </div>
                                    </li>
                                </draggable>
                                <div class="card-footer text-right">
                                    <button type="button" class="btn btn-sm btn-success btn-labeled" v-on:click="openSectionFormModal()">
                                        <span class="btn-label"><i class="fas fa-fw fa-sm fa-plus"></i></span> Add Section
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-header"><h4 class="h6 font-weight-bold mb-0">Passages</h4></div>
                                <draggable tag="ul"
                                           class="list-group list-group-hover list-group-flush"
                                           v-bind:draggable="'.element'"
                                           v-bind:handle="'.btn-handle'"
                                           v-on:update="sortPassages">
                                    <li v-if="loading.passages" class="list-group-item">
                                        @component('elements.progressbars.thin') @endcomponent
                                    </li>
                                    <li v-if="current.section.passages.length === 0 && current.section.id === '' && !loading.passages" class="list-group-item">
                                        Please select a Section.
                                    </li>
                                    <li v-if="current.section.passages.length === 0 && current.section.id !== '' && !loading.passages" class="list-group-item">
                                        No passages in <strong>@{{ current.section.name }}</strong>. Add a new passage.
                                    </li>
                                    <li v-if="current.section.passages.length > 0 && !loading.passages" v-for="passage in current.section.passages"
                                        class="list-group-item element cursor-pointer list-group-item-action"
                                        v-bind:class="{ 'list-group-item-primary': passage.id == current.passage.id }"
                                        v-on:click="selectPassage(passage)"
                                        v-bind:key="passage.name"
                                    >
                                        <div class="d-flex align-items-center">
                                            <span class="handle mr-2">
                                                <button type="button" class="btn-handle btn btn-xs btn-outline-dark" v-b-tooltip.hover title="Drag the handle to sort">
                                                    <i class="fas fa-fw fa-sm fa-arrows-alt-v"></i>
                                                </button>
                                            </span>
                                            <span class="name flex-fill mr-2">@{{ passage.name }}</span>
                                            <span class="buttons text-right">
                                                <button type="button" class="btn btn-xs btn-info" v-b-tooltip.hover title="Edit" v-on:click="openPassageFormModal(passage)">
                                                    <i class="fas fa-fw fa-sm fa-pencil-alt"></i>
                                                </button>
                                                <button type="button" class="btn btn-xs btn-danger" v-b-tooltip.hover title="Delete" v-on:click="openConfirmDeletePassageModal(passage)">
                                                    <i class="fas fa-fw fa-sm fa-times"></i>
                                                </button>
                                            </span>
                                        </div>
                                    </li>
                                </draggable>
                                <div class="card-footer text-right">
                                    <button type="button" class="btn btn-sm btn-success btn-labeled" v-on:click="openPassageFormModal()" v-bind:disabled="current.section.id === ''">
                                        <span class="btn-label"><i class="fas fa-fw fa-sm fa-plus"></i></span> Add Passage
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header"><h4 class="h6 font-weight-bold mb-0">Questions</h4></div>
                                <draggable tag="ul"
                                           class="list-group list-group-hover list-group-flush"
                                           v-bind:draggable="'.element'"
                                           v-bind:handle="'.btn-handle'"
                                           v-on:update="sortQuestions">
                                    <li v-if="loading.questions" class="list-group-item">
                                        @component('elements.progressbars.thin') @endcomponent
                                    </li>
                                    <li v-if="current.passage.questions.length === 0 && current.passage.id === '' && !loading.questions" class="list-group-item">
                                        Please select a Passage.
                                    </li>
                                    <li v-if="current.passage.questions.length === 0 && current.passage.id !== '' && !loading.questions" class="list-group-item">
                                        No questions in <strong>@{{ current.passage.name }}</strong>. Add a new question.
                                    </li>
                                    <li v-if="current.passage.questions.length > 0 && !loading.questions" v-for="question in current.passage.questions"
                                        class="list-group-item element cursor-pointer list-group-item-action"
                                        v-bind:key="question.id"
                                    >
                                        <div class="d-flex align-items-center">
                                            <span class="handle mr-2">
                                                <button type="button" class="btn-handle btn btn-xs btn-outline-dark" v-b-tooltip.hover title="Drag the handle to sort">
                                                    <i class="fas fa-fw fa-sm fa-arrows-alt-v"></i>
                                                </button>
                                            </span>
                                            <span class="name flex-fill mr-2">@{{ question.name }}</span>
                                            <span class="buttons text-right">
                                                <button type="button" class="btn btn-xs btn-info" v-b-tooltip.hover title="Edit" v-on:click="openQuestionFormModal(question)">
                                                    <i class="fas fa-fw fa-sm fa-pencil-alt"></i>
                                                </button>
                                                <button type="button" class="btn btn-xs btn-danger" v-b-tooltip.hover title="Delete" v-on:click="openConfirmDeleteQuestionModal(question)">
                                                    <i class="fas fa-fw fa-sm fa-times"></i>
                                                </button>
                                            </span>
                                        </div>
                                    </li>
                                </draggable>
                                <div class="card-footer text-right">
                                    <button type="button" class="btn btn-sm btn-success btn-labeled" v-on:click="openQuestionFormModal()" v-bind:disabled="current.section.id === ''">
                                        <span class="btn-label"><i class="fas fa-fw fa-sm fa-plus"></i></span> Add Question
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section Form Modal Start -->
                    <b-modal id="section-form-modal"
                             no-close-on-backdrop
                             size="xl"
                             v-bind:header-bg-variant="'info'"
                             v-bind:header-text-variant="'white'"
                             v-on:ok="submitSection">
                        <template v-slot:modal-header="{ close }">
                            <h5 class="modal-title" id="entity-section-form-title">
                                <span v-if="current.section.id === ''">Create New Section</span>
                                <span v-if="current.section.id !== ''">Edit Section</span>
                            </h5>
                            <button type="button" class="close text-white" v-on:click="close()" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </template>
                        <template v-slot:default>
                            <form id="section-form" ref="sectionForm" v-on:submit.prevent="submitSection" data-vv-scope="section">
                                <div class="form-group">
                                    <label for="section-name">Name <sup class="text-danger"><i class="fas fa-sm fa-asterisk"></i></sup></label>
                                    <input type="text" id="section-name" name="name" class="form-control"
                                           v-model="current.section.name"
                                           v-validate="'required'"
                                           v-bind:class="{ 'is-invalid': errors.has('section.name') }"
                                    >
                                </div>
                                <div class="form-group">
                                    <label for="type">Section Type <sup class="text-danger"><i class="fas fa-sm fa-asterisk"></i></sup></label>
                                    <v-select label="name" v-model="current.section.type" v-bind:clearable="false" v-bind:options="section.types.items" v-validate="'required'"></v-select>
                                    <div v-for="error in errors.collect('current.section.type')" class="invalid-feedback">@{{ error }}</div>
                                </div>
                                <!--
                                <div class="form-group">
                                    <label for="section-name">Abbreviation</label>
                                    <input type="text" id="abbreviation" name="abbreviation" class="form-control"
                                           v-model="current.section.abbreviation"
                                           v-validate="'required'"
                                           v-bind:class="{ 'is-invalid': errors.has('current.section.abbreviation') }"
                                    >
                                </div>
                                -->
                                <div class="form-group">
                                    <label for="section-name">Time Limit</label>
                                    <div class="input-group col-md-3">
                                        <input type="number" id="time_limit" name="time_limit" class="form-control"
                                               v-model="current.section.time_limit"
                                               v-validate="'required'"
                                               v-bind:class="{ 'is-invalid': errors.has('current.section.time_limit') }"
                                        >
                                        <div class="input-group-append">
                                            <span class="input-group-text">minutes</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mb-0">
                                    <label for="section-description">Description</label>
                                    <editor id="section-description"
                                            v-bind:key="keys.section.description"
                                            v-bind:key="keys.section.description"
                                            v-model="current.section.description"
                                            v-bind:init="config.description"></editor>
                                </div>
                            </form>
                        </template>
                        <template v-slot:modal-footer="{ cancel, ok }">
                            <button type="button" class="btn btn-secondary btn-labeled" v-on:click="cancel()">
                                <span class="btn-label"><i v-bind:class="{ 'fa-cog fa-spin': submitting, 'fa-times': !submitting }" class="fas fa-fw fa-times"></i></span> Cancel
                            </button>
                            <button type="submit" class="btn btn-success btn-labeled" v-on:click="ok()">
                                <span class="btn-label"><i v-bind:class="{ 'fa-cog fa-spin': submitting, 'fa-check': !submitting }" class="fas fa-fw fa-check"></i></span> Submit
                            </button>
                        </template>
                    </b-modal>
                    <!-- Section Form Modal End -->

                    <!-- Section Delete Modal Start -->
                    <b-modal id="section-delete-modal"
                             no-close-on-backdrop
                             size="xl"
                             v-bind:header-bg-variant="'danger'"
                             v-bind:header-text-variant="'white'"
                             v-on:ok="deleteSection(current.section)">
                        <template v-slot:modal-header="{ close }">
                            <h5 class="modal-title" id="entity-section-form-title">Delete Section</h5>
                            <button type="button" class="close text-white" v-on:click="close()" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </template>
                        <template v-slot:default>
                            Are you sure that you will delete the Section named <strong>@{{ current.section.name  }}</strong>? Passages, Questions, and Choices associated to the Section
                            will also be deleted!
                        </template>
                        <template v-slot:modal-footer="{ cancel, ok }">
                            <button type="button" class="btn btn-secondary btn-labeled" v-on:click="cancel()">
                                <span class="btn-label"><i v-bind:class="{ 'fa-cog fa-spin': submitting, 'fa-times': !submitting }" class="fas fa-fw fa-times"></i></span> No
                            </button>
                            <button type="submit" class="btn btn-danger btn-labeled" v-on:click="ok()">
                                <span class="btn-label"><i v-bind:class="{ 'fa-cog fa-spin': submitting, 'fa-check': !submitting }" class="fas fa-fw fa-exclamation-triangle"></i></span> Yes
                            </button>
                        </template>
                    </b-modal>
                    <!-- Section Delete Modal End -->

                    <!-- Passage Form Modal Start -->
                    <b-modal id="passage-form-modal"
                             no-close-on-backdrop
                             size="xl"
                             v-bind:header-bg-variant="'info'"
                             v-bind:header-text-variant="'white'"
                             v-on:ok="submitPassage">
                        <template v-slot:modal-header="{ close }">
                            <h5 class="modal-title" id="entity-passage-form-title">
                                <span v-if="current.passage.id === ''">Create New Passage</span>
                                <span v-if="current.passage.id !== ''">Edit Passage</span>
                            </h5>
                            <button type="button" class="close text-white" v-on:click="close()" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </template>
                        <template v-slot:default>
                            <form id="passage-form" ref="passageForm" v-on:submit.prevent="submitPassage" data-vv-scope="passage">
                                <div class="form-group">
                                    <label for="passage-name">Name <sup class="text-danger"><i class="fas fa-sm fa-asterisk"></i></sup></label>
                                    <input type="text" id="passage-name" name="name" class="form-control"
                                           v-model="current.passage.name"
                                           v-validate="'required'"
                                           v-bind:class="{ 'is-invalid': errors.has('passage.name') }"
                                    >
                                </div>
                                <div class="form-group form-row">
                                    <label class="col-md-2">Question Card Layout</label>
                                    <div class="col">
                                        <b-form-radio v-model="current.passage.qa_layout" name="some-radios" value="nqa">
                                            Question and Answer Only
                                        </b-form-radio>
                                        <b-form-radio v-model="current.passage.qa_layout" name="some-radios" value="pqa">
                                            Passage | Question and Answer
                                        </b-form-radio>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="passage-description">Description</label>
                                    <editor id="passage-description"
                                            v-bind:key="keys.passage.description"
                                            v-model="current.passage.description"
                                            v-bind:init="config.description"></editor>
                                </div>
                                <div class="form-group">
                                    <label for="passage-content">Content</label>
                                    <editor id="passage-content"
                                            v-bind:key="keys.passage.content"
                                            v-model="current.passage.content"
                                            v-bind:init="config.content"></editor>
                                </div>
                            </form>
                        </template>
                        <template v-slot:modal-footer="{ cancel, ok }">
                            <button type="button" class="btn btn-secondary btn-labeled" v-on:click="cancel()">
                                <span class="btn-label"><i v-bind:class="{ 'fa-cog fa-spin': submitting, 'fa-times': !submitting }" class="fas fa-fw fa-times"></i></span> Cancel
                            </button>
                            <button type="submit" class="btn btn-success btn-labeled" v-on:click="ok()">
                                <span class="btn-label"><i v-bind:class="{ 'fa-cog fa-spin': submitting, 'fa-check': !submitting }" class="fas fa-fw fa-check"></i></span> Submit
                            </button>
                        </template>
                    </b-modal>
                    <!-- Passage Form Modal End -->

                    <!-- Passage Delete Modal Start -->
                    <b-modal id="passage-delete-modal"
                             no-close-on-backdrop
                             size="xl"
                             v-bind:header-bg-variant="'danger'"
                             v-bind:header-text-variant="'white'"
                             v-on:ok="deletePassage(current.passage)">
                        <template v-slot:modal-header="{ close }">
                            <h5 class="modal-title" id="entity-passage-form-title">Delete Passage</h5>
                            <button type="button" class="close text-white" v-on:click="close()" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </template>
                        <template v-slot:default>
                            Are you sure that you will delete the Passage named <strong>@{{ current.passage.name  }}</strong>? Questions and Choices associated to the Passage
                            will also be deleted!
                        </template>
                        <template v-slot:modal-footer="{ cancel, ok }">
                            <button type="button" class="btn btn-secondary btn-labeled" v-on:click="cancel()">
                                <span class="btn-label"><i v-bind:class="{ 'fa-cog fa-spin': submitting, 'fa-times': !submitting }" class="fas fa-fw fa-times"></i></span> No
                            </button>
                            <button type="submit" class="btn btn-danger btn-labeled" v-on:click="ok()">
                                <span class="btn-label"><i v-bind:class="{ 'fa-cog fa-spin': submitting, 'fa-check': !submitting }" class="fas fa-fw fa-exclamation-triangle"></i></span> Yes
                            </button>
                        </template>
                    </b-modal>
                    <!-- Passage Delete Modal End -->

                    <!-- Question Form Modal Start -->
                    <b-modal id="question-form-modal"
                             no-close-on-backdrop
                             size="xl"
                             v-bind:header-bg-variant="'info'"
                             v-bind:header-text-variant="'white'"
                             v-on:ok="submitQuestion">
                        <template v-slot:modal-header="{ close }">
                            <h5 class="modal-title" id="entity-question-form-title">
                                <span v-if="current.question.id === ''">Create New Question</span>
                                <span v-if="current.question.id !== ''">Edit Question</span>
                            </h5>
                            <button type="button" class="close text-white" v-on:click="close()" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </template>
                        <template v-slot:default>
                            <form id="question-form" ref="questionForm" v-on:submit.prevent="submitQuestion" data-vv-scope="question">
                                <div class="form-group row">
                                    <label for="question-type" class="col-sm-2 col-form-label">Question Type</label>
                                    <div class="col-sm-6">
                                        <v-select id="question-type" name="type" label="name" v-model="current.question.type"
                                                  v-bind:clearable="false"
                                                  v-bind:options="selections.question_types"
                                                  v-validate="'required'"></v-select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="question-type" class="col-sm-2 col-form-label">Problem Types</label>
                                    <div class="col">
                                        <tags-input element-id="problem-types"
                                                    v-model="current.question.problem_types"
                                                    v-bind:existing-tags="section.tags.items"
                                                    v-bind:wrapper-class="'form-control h-100'"
                                                    v-bind:typeahead="true"
                                                    v-bind:typeahead-style="'dropdown'">
                                        </tags-input>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="question-difficulty" class="col-sm-2 col-form-label">Difficulty</label>
                                    <div class="col-sm-6">
                                        <vue-slider name="difficulty"
                                                    v-model="current.question.difficulty"
                                                    v-bind:adsorb="true"
                                                    v-bind:interval="1"
                                                    v-bind:min="1"
                                                    v-bind:max="5"
                                                    v-bind:marks="true"
                                                    v-validate="'required'"></vue-slider>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="question-explain" class="col-sm-2 col-form-label">Explanation Video</label>
                                    <div class="col">
                                        <div class="input-group">
                                            <input type="text" class="form-control" aria-describedby="btn-file-video" v-model="current.question.explain_video">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-primary" type="button" id="btn-file-video" v-on:click="openMediaManager('explain-video')"><i class="fas fa-fw fa-file-video"></i></button>
                                            </div>
                                        </div>
                                        <div class="mt-2" ng-if="current.question.explain_video != ''">
                                            <video width="300" controls>
                                                <source v-bind:src="current.question.explain_video">
                                            </video>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Shuffle Choices</label>
                                    <div class="col-sm-6 d-flex align-items-center">
                                        <b-form-radio-group v-model="current.question.shuffle_choices">
                                            <b-form-radio value="2">Yes</b-form-radio>
                                            <b-form-radio value="1">No</b-form-radio>
                                            <b-form-radio value="0">Inherit from Quiz Settings</b-form-radio>
                                        </b-form-radio-group>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="question-description">Question <sup class="text-danger"><i class="fas fa-sm fa-asterisk"></i></sup></label>
                                    <editor
                                            id="question-description"
                                            v-bind:key="keys.question.description"
                                            v-model="current.question.question"
                                            v-bind:init="config.question"></editor>
                                </div>

                                <div class="card">
                                    <div class="card-header"><h4 class="h6 font-weight-bold mb-0">Choices</h4></div>
                                    <draggable tag="ul"
                                               class="list-group list-group-hover list-group-flush"
                                               v-bind:draggable="'.element'"
                                               v-bind:handle="'.btn-handle'"
                                               v-on:update="sortChoices"
                                    >
                                        <li v-if="current.question.choices.length === 0" class="list-group-item">
                                            No choices in the quiz. Add a new choice.
                                        </li>
                                        <li v-if="current.question.choices.length > 0" v-for="(choice, i) in current.question.choices"
                                            class="list-group-item element list-group-item-action"
                                            v-bind:key="choice.ud">
                                            <div class="d-flex">
                                                <div class="handle mr-2">
                                                    <button type="button" class="btn-handle btn btn-xs btn-outline-dark" v-b-tooltip.hover title="Drag the handle to sort">
                                                        <i class="fas fa-fw fa-sm fa-arrows-alt-v"></i>
                                                    </button>
                                                </div>
                                                <div class="choice flex-fill mr-2">
                                                    <editor
                                                            v-bind:id="choice.td"
                                                            v-bind:key="choice.ud"
                                                            v-model="choice.choice"
                                                            v-bind:init="config.choice"></editor>
                                                    <!--
                                                    <textarea class="form-control" v-model="choice.choice" rows="5"></textarea>
                                                    -->
                                                </div>
                                                <div class="correct mr-2">
                                                    <b-form-checkbox v-model="choice.is_correct"
                                                                     size="lg"
                                                                     switch
                                                                     v-on:change="changePoints($event, choice)"
                                                                     v-b-tooltip.hover
                                                                     title="Correct">
                                                    </b-form-checkbox>
                                                </div>
                                                <div class="points mr-2">
                                                    <input type="number"
                                                           class="form-control form-control-sm" style="width: 3rem"
                                                           v-model="choice.points"
                                                           v-b-tooltip.hover
                                                           v-bind:disabled="!choice.is_correct"
                                                           title="Points">
                                                </div>
                                                <div class="buttons text-right">
                                                    <button type="button" class="btn btn-xs btn-danger" v-b-tooltip.hover title="Delete" v-on:click="deleteChoice(i)">
                                                        <i class="fas fa-fw fa-sm fa-times"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </li>
                                    </draggable>
                                    <div class="card-footer text-right">
                                        <button type="button" class="btn btn-sm btn-success btn-labeled" v-on:click="addChoice()">
                                            <span class="btn-label"><i class="fas fa-fw fa-sm fa-plus"></i></span> Add Choice
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </template>
                        <template v-slot:modal-footer="{ cancel, ok }">
                            <button type="button" class="btn btn-secondary btn-labeled" v-on:click="cancel()">
                                <span class="btn-label"><i v-bind:class="{ 'fa-cog fa-spin': submitting, 'fa-times': !submitting }" class="fas fa-fw fa-times"></i></span> Cancel
                            </button>
                            <button type="submit" class="btn btn-success btn-labeled" v-on:click="ok()">
                                <span class="btn-label"><i v-bind:class="{ 'fa-cog fa-spin': submitting, 'fa-check': !submitting }" class="fas fa-fw fa-check"></i></span> Submit
                            </button>
                        </template>
                    </b-modal>
                    <!-- Question Form Modal End -->

                    <!-- Question Delete Modal Start -->
                    <b-modal id="question-delete-modal"
                             no-close-on-backdrop
                             size="xl"
                             v-bind:header-bg-variant="'danger'"
                             v-bind:header-text-variant="'white'"
                             v-on:ok="deleteQuestion(current.question)">
                        <template v-slot:modal-header="{ close }">
                            <h5 class="modal-title" id="entity-question-form-title">Delete Question</h5>
                            <button type="button" class="close text-white" v-on:click="close()" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </template>
                        <template v-slot:default>
                            Are you sure that you will delete the Question named <strong>@{{ current.question.name  }}</strong>? Choices associated to the Question
                            will also be deleted!
                        </template>
                        <template v-slot:modal-footer="{ cancel, ok }">
                            <button type="button" class="btn btn-secondary btn-labeled" v-on:click="cancel()">
                                <span class="btn-label"><i v-bind:class="{ 'fa-cog fa-spin': submitting, 'fa-times': !submitting }" class="fas fa-fw fa-times"></i></span> No
                            </button>
                            <button type="submit" class="btn btn-danger btn-labeled" v-on:click="ok()">
                                <span class="btn-label"><i v-bind:class="{ 'fa-cog fa-spin': submitting, 'fa-check': !submitting }" class="fas fa-fw fa-exclamation-triangle"></i></span> Yes
                            </button>
                        </template>
                    </b-modal>
                    <!-- Question Delete Modal End -->

                    <!-- Media Manager Modal Start -->
                    <b-modal id="mediamanager-modal"
                             no-close-on-backdrop
                             size="xl"
                             v-bind:header-bg-variant="'primary'"
                             v-bind:header-text-variant="'white'"
                             v-on:ok="">
                        <template v-slot:modal-header="{ close }">
                            <h5 class="modal-title" id="entity-passage-form-title">Media Manager</h5>
                            <button type="button" class="close text-white" v-on:click="close()" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </template>
                        <template v-slot:default>
                            <iframe width="100%" height="100%" frameborder="0" allowtransparency="true" src="{{ route('mediamanager.modal', ['type' => 'video']) }}"></iframe>
                        </template>
                        <template v-slot:modal-footer="{ cancel, ok }">
                            <button type="button" class="btn btn-secondary btn-labeled" v-on:click="cancel()">
                                <span class="btn-label"><i class="fas fa-fw fa-times"></i></span> Close
                            </button>
                        </template>
                    </b-modal>
                    <!-- Media Manager Modal End -->
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script>Quiz = {!! $quiz->toJson() !!};</script>
    <script src="{{ asset('js/tinymce/tinymce.js') }}"></script>
    <script src="{{ asset('js/components/quizzes/quizzes/questionnaire.js') }}"></script>
@endsection
