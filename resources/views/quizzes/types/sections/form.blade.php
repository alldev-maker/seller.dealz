@extends('layout')

@section('content')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('quizzes.index')  }}">Quizzes Management</a></li>
        <li class="breadcrumb-item"><a href="{{ route('quizzes.types.index')  }}">Types</a></li>
        <li class="breadcrumb-item"><a href="{{ route('quizzes.types.sections.index')  }}">Section Types</a></li>
        <li class="breadcrumb-item active" aria-current="page"><?php echo $section->id > 0 ? 'Edit' : 'Add New'; ?></li>
    </ol>
</nav>
<div class="container-fluid px-3">
    <div id="type" class="row">
        <div class="col">
            @component('elements.spinner') @endcomponent
            <div class="content" v-cloak>
                <form v-on:submit.prevent="submit" data-vv-scope="role">
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-md-8">
                                <div class="card mb-3">
                                    <div class="card-header"><h2 class="h6 m-0 font-weight-bold">General Information</h2></div>
                                    <div class="card-body">
                                        <div class="form-group row">
                                            <label for="name" class="col-sm-3">
                                                Name <sup class="text-danger"><i class="fas fa-sm fa-asterisk"></i></sup>
                                            </label>
                                            <div class="col">
                                                <input type="text" id="name" name="name" class="form-control"
                                                       v-model="section.name"
                                                       v-validate="'required'"
                                                       v-bind:class="{ 'is-invalid': errors.has('section.name') }"
                                                >
                                                <div v-if="errors.has('section.name')" class="invalid-feedback">Required</div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="name" class="col-sm-3">Slug</label>
                                            <div class="col">
                                                <input type="text" id="name" name="name" class="form-control"
                                                       v-model="section.slug"
                                                >
                                            </div>
                                        </div>
                                        <div class="form-group row mb-0">
                                            <label for="slug" class="col-sm-3">Description</label>
                                            <div class="col">
                                                <textarea id="description" name="description" class="form-control" rows="7" v-model="section.description"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <p class="m-0 text-center">
                                    <button class="btn btn-lg btn-primary btn-labeled" v-bind:disabled="errors.any('role')">
                                <span class="btn-label">
                                    <i v-bind:class="{ 'fa-cog fa-spin': submitting, 'fa-upload': !submitting }" class="fas fa-fw"></i></span> Submit
                                    </button>
                                    <a class="btn btn-lg btn-secondary btn-labeled" href="{{ route('quizzes.types.sections.index') }}">
                                        <span class="btn-label"><i v-bind:class="{ 'fa-cog fa-spin': submitting, 'fa-chevron-left': !submitting }" class="fas fa-fw"></i></span> Go Back
                                    </a>
                                </p>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

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
                                <p class="mb-0">Are you sure that you will delete the @{{ name.singular }} named <strong>@{{ section.name }}</strong>?</p>
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
    </div>
</div>
@endsection

@section('javascript')
    <script>window.quizmaster.sectiontype = @json($section);</script>
    <script src="{{ asset('js/components/quizzes/types/sections/form.js') }}"></script>
@endsection