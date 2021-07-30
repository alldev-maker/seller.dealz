@extends('layout')

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('quizzes.index')  }}">Quizzes Management</a></li>
            <li class="breadcrumb-item active" aria-current="page">Results</li>
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
                                <div class="filter-group pr-3 d-flex">
                                    <div class="align-self-center pr-2">
                                        Filters:
                                        <span v-if="filters.status === 0" class="font-weight-bold">Off</span>
                                        <span v-if="filters.status === 1" class="font-weight-bold text-success">On</span>
                                    </div>
                                    <div class="btn-group" role="group">
                                        <button class="btn" v-bind:class="{ 'btn-outline-primary': filters.status === 0, 'btn-primary': filters.status === 1}" v-b-tooltip.hover title="Set filters" v-on:click="openFiltersModal()"><i class="fas fa-fw fa-filter"></i></button>
                                        <button class="btn btn-outline-primary" v-b-tooltip.hover title="Clear filters" v-on:click="clearFilters()"><i class="fas fa-fw fa-broom"></i></button>
                                    </div>
                                </div>
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
                                        <th class="date date-timestamp">
                                            <div v-if="checkbox.ids.length > 0">
                                                <button class="btn btn-xs btn-danger" v-b-tooltip.hover title="Delete Selected" v-on:click="confirmRemoveSelected()">
                                                    <i class="fas fa-fw fa-trash"></i>
                                                </button>
                                            </div>
                                            <div v-else>Date Taken</div>
                                        </th>
                                        <th class="">Test Taker</th>
                                        <th class="name">Quiz</th>
                                        <th class="total">Score</th>
                                        <th class="total">Answered</th>
                                        <th class="total">Skipped</th>
                                        <th class="actions actions-sm">Actions</th>
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
                                        <td class="date date-timestamp text-monospace">@{{ entity.time_start }}</td>
                                        <td class="">@{{ entity.test_taker_name }}</td>
                                        <td class="name">@{{ entity.quiz_name }}</td>
                                        <td class="total">@{{ entity.score }}</td>
                                        <td class="total">@{{ entity.questions_answered }}</td>
                                        <td class="total">@{{ entity.questions_skipped }}</td>
                                        <td class="actions actions-md">
                                            <a class="btn btn-xs btn-info" v-b-tooltip.hover title="Summary" v-bind:href="entity.urls.summary"><i class="fas fa-fw fa-sm fa-window-maximize"></i></a>
                                            <a class="btn btn-xs btn-info" v-b-tooltip.hover title="Chart" v-bind:href="entity.urls.chart"><i class="fas fa-fw fa-sm fa-chart-line"></i></a>
                                            <button type="button" class="btn btn-xs btn-info" v-b-tooltip.hover title="Populate Problem Types" v-on:click="populateProblemTypes(entity)"><i class="fas fa-fw fa-sm fa-cog"></i></button>
                                            <button type="button" class="btn btn-xs btn-info" v-b-tooltip.hover title="Recalculate" v-on:click="recalculate(entity)"><i class="fas fa-fw fa-sm fa-calculator"></i></button>
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

                <!-- Filter Modal Start -->
                <div class="modal fade" id="entity-filter" tabindex="-1" role="dialog" aria-labelledby="entity-filter-title" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content border-0">
                            <form v-on:submit.prevent="filter">
                                <div class="modal-header text-white bg-primary">
                                    <h5 class="modal-title" id="entity-filter-title">Filters</h5>
                                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group row">
                                        <label for="question-type" class="col-sm-2 col-form-label">Quiz</label>
                                        <div class="col">
                                            <v-select id="question-type" name="type" label="name" v-model="filters.form.quiz"
                                                      v-bind:clearable="false"
                                                      v-bind:options="quizzes.items"></v-select>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer bg-light">
                                    <button type="button" class="btn btn-secondary btn-labeled" data-dismiss="modal">
                                        <span class="btn-label"><i class="fas fa-fw fa-times"></i></span> Close
                                    </button>
                                    <button type="submit" class="btn btn-primary btn-labeled">
                                        <span class="btn-label"><i class="fas fa-fw fa-filter"></i></span> Filter
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- Filter Modal End -->

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
                                    <p class="mb-0">Are you sure that you will delete the @{{ name.singular }} ID <strong>@{{ result.id }}</strong>?</p>
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
                                    <p class="mb-0">Are you sure that you will delete the selected @{{ name.plural }}</strong>?</p>
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
    <script>window.quizmaster.message = @json($message); </script>
    <script src="{{ asset('js/components/quizzes/results/index.js') }}"></script>
@endsection