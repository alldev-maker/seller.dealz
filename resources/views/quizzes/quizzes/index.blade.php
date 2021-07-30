@extends('layout')

@section('title')
    Quizzes {{ ts() }} Quizzes Management {{ ts() }} {{ settings('site.title') }}
@endsection

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('quizzes.index')  }}">Quizzes Management</a></li>
            <li class="breadcrumb-item active" aria-current="page">Quizzes</li>
        </ol>
    </nav>
    <div class="container-fluid px-3">
        <div id="component" class="row">
            <div class="col">
                @component('elements.spinner') @endcomponent
                <div class="content" v-cloak>
                    <div class="table-grid border rounded">
                        <div class="section section-t border-bottom rounded-top p-2 d-flex justify-content-between align-items-center">
                            <div class="d-flex w-50 justify-content-start">
                                <form method="get" class="d-flex w-100" v-on:submit.prevent="search()">
                                    <div class="input-group">
                                        <input type="text" class="form-control" placeholder="Search" v-model="query.keywords" aria-label="Search">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-primary" type="submit" v-b-tooltip.hover title="Perform search">
                                                <i class="fas fa-fw fa-search"></i>
                                            </button>
                                            <button class="btn btn-primary" type="button" v-on:click="clear()" v-b-tooltip.hover title="Clear all">
                                                <i class="fas fa-fw fa-broom"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="button-area ml-auto d-flex">
                                <button class="btn btn-labeled btn-primary" v-on:click="openCreateModal()">
                                    <span class="btn-label"><i class="fas fa-fw fa-sm fa-plus"></i></span> Create New
                                </button>
                            </div>
                        </div>
                        <div class="section section-main">
                            <table class="table table-bordered table-hover bg-white mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="checkbox"><input type="checkbox" v-model="checkbox.all"></th>
                                        <th class="">
                                            <div v-if="checkbox.ids.length > 0">
                                                <button class="btn btn-xs btn-danger" v-b-tooltip.hover title="Delete Selected" v-on:click="confirmRemoveSelected()">
                                                    <i class="fas fa-fw fa-trash"></i>
                                                </button>
                                            </div>
                                            <div v-else>Name</div>
                                        </th>
                                        @role(['admin', 'developer'])
                                        <th class="name">Owner</th>
                                        @endrole
                                        <th class="icon"><i class="fas fa-fw fa-external-link-square-alt fa-lg text-success" v-b-tooltip.hover title="Visible To Web"></i></th>
                                        <th class="actions actions-md">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="loading">
                                        <td colspan="10" class="text-center p-5">
                                            <img class="mb-3" src="{{ url('/images/spinner-loading.svg') }}" width="220" height="10">
                                            <div>Loading data...</div>
                                        </td>
                                    </tr>
                                    <tr v-if="results.total.records === 0 && !loading">
                                        <td colspan="10" class="text-center p-5">
                                            No results.
                                        </td>
                                    </tr>
                                    <tr v-if="results.total.records > 0 && !loading" v-for="(entity, i) of results.rows">
                                        <td class="checkbox">
                                            <input type="checkbox" v-model="checkbox.ids" v-bind:value="entity.id" v-on:click="select">
                                        </td>
                                        <td class=""> @{{ entity.name }}</td>
                                        @role(['admin', 'developer'])
                                        <td class="name"> @{{ entity.user.nice_name }}</td>
                                        @endrole
                                        <td class="icon">
                                            <span class="text-success fas fa-fw fa-circle" v-bind:class="{ 'text-success': entity.enabled, 'text-muted': !entity.enabled }"></span>
                                        </td>
                                        <td class="actions actions-md">
                                            <a class="btn btn-xs btn-info" v-b-tooltip.hover title="Edit About" v-bind:href="entity.urls.edit"><i class="fas fa-fw fa-sm fa-pencil-alt"></i></a>
                                            <a class="btn btn-xs btn-info" v-b-tooltip.hover title="Questionnaire" v-bind:href="entity.urls.questionnaire"><i class="fas fa-fw fa-sm fa-list"></i></a>
                                            <a class="btn btn-xs btn-info" v-b-tooltip.hover title="Settings" v-bind:href="entity.urls.settings"><i class="fas fa-fw fa-sm fa-cog"></i></a>
                                            <a class="btn btn-xs btn-info" v-b-tooltip.hover title="Preview" v-bind:href="entity.urls.view" target="_blank"><i class="fas fa-fw fa-sm fa-eye"></i></a>
                                            <button type="button" class="btn btn-xs btn-info" v-b-tooltip.hover title="Copy" v-on:click="copy(entity.id)"><i class="fas fa-fw fa-sm fa-clone"></i></button>
                                            <a class="btn btn-xs btn-success"
                                               v-bind:class="{ disabled: !entity.enabled }"
                                               v-b-tooltip.hover
                                               title="Frontend"
                                               v-bind:href="entity.enabled ? entity.urls.frontend : '#'" target="_blank">
                                                <i class="fas fa-fw fa-sm fa-external-link-alt"></i>
                                            </a>
                                            <button type="button" class="btn btn-xs btn-danger" v-b-tooltip.hover title="Delete" v-on:click="confirmRemove(entity)"><i class="fas fa-fw fa-sm fa-trash"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="section section-b border-top rounded-bottom p-2 d-flex justify-content-between align-items-center">
                            <div class="total">Records: @{{ results.total.records }} &#8226; Page @{{ query.page }} of @{{ results.total.pages }}</div>
                            <b-pagination
                                    class="mb-0"
                                    v-model="query.page"
                                    v-bind:total-rows="results.total.records"
                                    v-bind:per-page="query.limit"
                                    v-on:input="turn()"
                            >
                            </b-pagination>
                        </div>
                    </div>
                </div>

                <!-- Add Modal Start -->
                <div class="modal fade" id="entity-create" tabindex="-1" role="dialog" aria-labelledby="entity-remove-title" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content border-0">
                            <form v-on:submit.prevent="create">
                                <div class="modal-header text-white bg-info">
                                    <h5 class="modal-title" id="entity-remove-title">Create New @{{ name.singular }}</h5>
                                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group mb-0">
                                        <label for="name">Quiz Name <sup class="text-danger"><i class="fas fa-sm fa-asterisk"></i></sup></label>
                                        <input type="text" id="name" name="name" class="form-control"
                                               v-model="quiz.name"
                                               v-validate="'required'"
                                               v-bind:class="{ 'is-invalid': errors.has('quiz.name') }"
                                        >
                                    </div>
                                </div>
                                <div class="modal-footer bg-light">
                                    <button type="button" class="btn btn-secondary btn-labeled" data-dismiss="modal">
                                        <span class="btn-label"><i v-bind:class="{ 'fa-cog fa-spin': submitting, 'fa-times': !submitting }" class="fas fa-fw"></i></span> Cancel
                                    </button>
                                    <button type="submit" class="btn btn-success btn-labeled">
                                        <span class="btn-label"><i v-bind:class="{ 'fa-cog fa-spin': submitting, 'fa-check': !submitting }" class="fas fa-fw"></i></span> OK
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- Add Modal End -->

                <!-- Delete Modal Start -->
                <div class="modal fade" id="entity-remove" tabindex="-1" role="dialog" aria-labelledby="entity-remove-title" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content border-0">
                            <form v-on:submit.prevent="remove">
                                <div class="modal-header text-white bg-danger">
                                    <h5 class="modal-title" id="entity-remove-title">Delete @{{ name.singular }}</h5>
                                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <p class="mb-0">Are you sure that you will delete the @{{ name.singular }} named <strong>@{{ quiz.name }}</strong>?</p>
                                </div>
                                <div class="modal-footer bg-light">
                                    <button type="button" class="btn btn-secondary btn-labeled" data-dismiss="modal">
                                        <span class="btn-label"><i v-bind:class="{ 'fa-cog fa-spin': submitting, 'fa-times': !submitting }" class="fas fa-fw"></i></span> No
                                    </button>
                                    <button type="submit" class="btn btn-danger btn-labeled">
                                        <span class="btn-label"><i v-bind:class="{ 'fa-cog fa-spin': submitting, 'fa-exclamation-triangle': !submitting }" class="fas fa-fw"></i></span> Yes
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- Delete Modal End -->

                <!-- Delete Selected Modal Start -->
                <div class="modal fade" id="entity-remove-selected" tabindex="-1" role="dialog" aria-labelledby="entity-remove-selected-title" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content border-0">
                            <form v-on:submit.prevent="removeSelected">
                                <div class="modal-header text-white bg-danger">
                                    <h5 class="modal-title" id="entity-remove-selected-title">Delete Selected @{{ name.plural }}</h5>
                                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <p class="mb-0">Are you sure that you will delete the selected @{{ name.plural }}?</p>
                                </div>
                                <div class="modal-footer bg-light">
                                    <button type="button" class="btn btn-secondary btn-labeled" data-dismiss="modal">
                                        <span class="btn-label"><i v-bind:class="{ 'fa-cog fa-spin': submitting, 'fa-times': !submitting }" class="fas fa-fw"></i></span> No
                                    </button>
                                    <button type="submit" class="btn btn-danger btn-labeled">
                                        <span class="btn-label"><i v-bind:class="{ 'fa-cog fa-spin': submitting, 'fa-exclamation-triangle': !submitting }" class="fas fa-fw"></i></span> Yes
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- Delete Selected Modal End -->
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script>Message = {!! json_encode($message) !!}; </script>
    <script src="{{ asset('js/components/quizzes/quizzes/index.js') }}"></script>
@endsection