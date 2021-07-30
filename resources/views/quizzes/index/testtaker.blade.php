@extends('layout')

@section('title')
    Quizzes :: {{ settings('site.title') }}
@endsection

@section('content')
<div class="component">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active">Quizzes</li>
        </ol>
    </nav>
    <div class="container-fluid">
        <div id="component" class="row">
            <div class="col">
                @component('elements.spinner') @endcompornent
                <div class="content pb-3" v-cloak>
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
                            <div class="button-area ml-auto d-flex"></div>
                        </div>
                        <div class="section section-main">
                            <table class="table table-bordered table-hover bg-white mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="">
                                            <div v-if="checkbox.ids.length > 0">
                                                <button class="btn btn-xs btn-danger" v-b-tooltip.hover title="Delete Selected" v-on:click="confirmRemoveSelected()">
                                                    <i class="fas fa-fw fa-trash"></i>
                                                </button>
                                            </div>
                                            <div v-else>Name</div>
                                        </th>
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
                                        <td class=""> @{{ entity.name }}</td>
                                        <td class="icon">
                                            <span class="text-success fas fa-fw fa-circle" v-bind:class="{ 'text-success': entity.enabled, 'text-muted': !entity.enabled }"></span>
                                        </td>
                                        <td class="actions actions-md">
                                            <a class="btn btn-xs btn-success"
                                               v-b-tooltip.hover
                                               title="Take the Quiz"
                                               v-bind:href="entity.urls.frontend" target="_blank">
                                                <i class="fas fa-fw fa-sm fa-feather-alt"></i>
                                            </a>
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
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script src="{{ asset('js/components/quizzes/quizzes/testtaker.js') }}"></script>
@endsection