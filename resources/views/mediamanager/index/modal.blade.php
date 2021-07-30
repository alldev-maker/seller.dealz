@extends('layout-modal')

@section('content')
    <div id="component" class="component">
        <div class="container-fluid">
            <div id="dropzone" class="d-none align-items-center justify-content-center">
                <div class="h3 font-weight-light">Drop images to upload.</div>
            </div>

            <div class="row">
                <div class="col px-0">
                    <div class="mmm-grid">
                        <div class="section section-t bg-light border-bottom p-2 d-flex justify-content-between">
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
                            <div class="button-area ml-auto">
                                <input id="stdFileUpload" ref="stdFileUpload" type="file" hidden multiple v-on:change="handleUpload()">
                                <button type="button" class="btn btn-primary btn-labeled" v-on:click="selectFiles"><span class="btn-label"><i class="fas fa-upload"></i></span>Upload</button>
                            </div>
                        </div>
                        <div v-if="loading && query.page === 1 && results.rows.length === 0" class="pt-2 text-center">
                            @component('elements.spinner') @endcomponent
                        </div>
                        <div v-cloak v-if="!loading && query.page === 1 && results.rows.length === 0" class="pt-2 text-center">
                            <div class="p-3">There are no files you are looking for.</div>
                        </div>
                        <div v-cloak v-if="(!loading || query.page > 1) && results.rows.length > 0" class="grid-wrapper pt-2">
                            <div v-for="file in results.rows" class="card card-file">
                                <img class="card-img-top bg-checkered cursor-pointer" v-on:click="pickFile(file)" v-bind:src="file.urls.thumbnail" width="100%" height="100%">
                                <div class="card-body p-2 border-top">
                                    <div class="actions text-center">
                                        <button type="button" class="btn btn-xs btn-primary" v-b-tooltip.hover title="View" v-on:click="openViewFileModal(file)"><i class="fas fa-fw fa-image"></i></button>
                                        <a class="btn btn-xs btn-primary" v-b-tooltip.hover title="External Link" v-bind:href="file.urls.source" target="_blank"><i class="fas fa-fw fa-external-link-alt"></i></a>
                                        <button type="button" class="btn btn-xs btn-danger" v-b-tooltip.hover title="Delete" v-on:click="openConfirmDeleteFileModal(file)"><i class="fas fa-fw fa-trash-alt"></i></button>
                                    </div>
                                </div>
                                <div class="card-footer p-2 text-monospace text-center small"
                                     v-b-tooltip.hover
                                     v-bind:title="file.name">
                                    @{{ file.name }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Progress Modal Start -->
        <b-modal id="progresses"
                 no-close-on-backdrop
                 size="lg"
                 scrollable
                 centered
                 hide-header
                 hide-footer
                 v-bind:header-bg-variant="'primary'"
                 v-bind:header-text-variant="'white'">
            <template v-slot:default>
                <div class="d-none">@{{ prog }}</div>
                <div class="uploaded-file mb-2" v-for="(file, index) in fileUpload.files">
                    <div class="description d-flex justify-content-between mb-1 text-monospace small">
                        <span class="d-flex w-50 filename ">@{{ index + 1 }}. @{{ file.name }}</span>
                        <span class="d-flex w-50 indicator justify-content-end">
                            <strong class="text-primary" v-if="file.done === null">@{{ file.progress }}%</strong>
                            <strong class="text-success" v-if="file.done === true">Done!</strong>
                            <strong class="text-danger" v-if="file.done === false">Failed! @{{ file.error }}</strong>
                        </span>
                    </div>
                    <b-progress height="5px" v-bind:value="file.progress" variant="primary" striped animated></b-progress>
                </div>
            </template>
        </b-modal>
        <!-- Progress Modal End -->

        <!-- View Modal Start -->
        <b-modal id="view-modal"
                 size="xl"
                 scrollable
                 v-bind:header-bg-variant="'primary'"
                 v-bind:header-text-variant="'white'">
            <template v-slot:modal-header="{ close }">
                <h5 class="modal-title" id="entity-section-form-title">View File [@{{ file.name }}]</h5>
                <button type="button" class="close text-white" v-on:click="close()" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </template>
            <template v-slot:default>
                <div class="d-flex justify-content-center align-items-center">
                    <img v-bind:src="file.urls.source" style="max-width: 100%">
                </div>
            </template>
            <template v-slot:modal-footer="{ close }">
                <button type="button" class="btn btn-secondary btn-labeled" v-on:click="close()">
                    <span class="btn-label"><i class="fas fa-fw fa-times"></i></span> Close
                </button>
            </template>
        </b-modal>
        <!-- View Modal Start -->

        <!-- Delete Modal Start -->
        <b-modal id="file-delete-modal"
                 no-close-on-backdrop
                 size="xl"
                 v-bind:header-bg-variant="'danger'"
                 v-bind:header-text-variant="'white'"
                 v-on:ok="deleteFile(file)">
            <template v-slot:modal-header="{ close }">
                <h5 class="modal-title" id="entity-section-form-title">Delete File</h5>
                <button type="button" class="close text-white" v-on:click="close()" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </template>
            <template v-slot:default>
                Are you sure that you will delete the file named <strong>@{{ file.name  }}</strong>? It will break the link of the
                image in the passages, questions, and choices!
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
        <!-- Delete Modal End -->
    </div>
@endsection

@section('javascript')
    <script>window.quizmaster.mm = { settings: @json(settings('mm.*')), type: '{{ $type }}' }</script>
    <script src="{{ asset('js/components/mediamanager/index.js') }}"></script>
@endsection