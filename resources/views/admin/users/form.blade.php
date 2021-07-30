@extends('layout')

@section('content')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.index')  }}">Administration</a></li>
        <li class="breadcrumb-item">Users</li>
        <li class="breadcrumb-item active" aria-current="page"><?php echo $user->id > 0 ? 'Edit' : 'Add New'; ?></li>
    </ol>
</nav>
<div class="container-fluid px-3">
    <div id="application" class="row">
        <div class="col">
            @component('elements.spinner') @endcomponent
            <div class="content" v-cloak>
                <form v-on:submit.prevent="submit" data-vv-scope="user">
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-md-4">
                                <div class="card mb-3">
                                    <div class="card-header"><h2 class="h6 m-0 font-weight-bold">Credentials</h2></div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="name">
                                                User Name <sup class="text-danger"><i class="fas fa-sm fa-asterisk"></i></sup>
                                            </label>
                                            <input type="text" id="name" name="name" class="form-control"
                                                   v-model="user.name"
                                                   v-validate="'required|min:3'"
                                                   v-bind:class="{ 'is-invalid': errors.has('user.name') }"
                                            >
                                            <div v-for="error in errors.collect('user.name')" class="invalid-feedback">@{{ error }}</div>
                                        </div>
                                        <div class="form-group">
                                            <label for="slug">Password</label>
                                            <input type="text" id="password" name="password" class="form-control" v-model="user.password">
                                        </div>
                                        <div class="form-group mb-0">
                                            <label for="role">Role <sup class="text-danger"><i class="fas fa-sm fa-asterisk"></i></sup></label>
                                            <v-select label="name" v-model="user.role" v-bind:clearable="false" v-bind:options="roles.items" v-validate="'required'"></v-select>
                                            <div v-for="error in errors.collect('user.role')" class="invalid-feedback">@{{ error }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="card mb-3">
                                    <div class="card-header"><h2 class="h6 m-0 font-weight-bold">General Information</h2></div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="nice_name">Full Name <sup class="text-danger"><i class="fas fa-sm fa-asterisk"></i></sup></label>
                                            <input type="text" id="nice_name" name="nice_name" class="form-control" v-model="user.nice_name"  v-validate="'required'">
                                            <div v-for="error in errors.collect('user.nice_name')" class="invalid-feedback">@{{ error }}</div>
                                        </div>
                                        <div class="form-group">
                                            <label for="name_nice">Email <sup class="text-danger"><i class="fas fa-sm fa-asterisk"></i></sup></label>
                                            <input type="email" id="email" name="email" class="form-control" v-model="user.email"  v-validate="'email|required'">
                                            <div v-for="error in errors.collect('user.email')" class="invalid-feedback">@{{ error }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card mb-3">
                                    <div class="card-header"><h2 class="h6 m-0 font-weight-bold">Admin Notes</h2></div>
                                    <div class="card-body">
                                        <div class="form-group mb-0">
                                            <textarea type="text" id="notes" name="notes" class="form-control" rows="7" v-model="user.notes"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <p class="mb-0 border-top pt-3 text-center">
                                    <button class="btn btn-lg btn-primary btn-labeled" v-bind:disabled="errors.any('user')">
                                        <span class="btn-label">
                                            <i v-bind:class="{ 'fa-cog fa-spin': submitting, 'fa-upload': !submitting }" class="fas fa-fw"></i>
                                        </span> Submit
                                    </button>
                                    <a class="btn btn-lg btn-secondary btn-labeled" href="<?php echo route('admin.users.index'); ?>">
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
                                <p class="mb-0">Are you sure that you will delete the @{{ name.singular }} named <strong>@{{ user.name }}</strong>?</p>
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
</div>
@endsection

@section('javascript')
    <script>window.quizmaster.user = @json($user);</script>
    <script src="{{ asset('js/components/admin/users/form.js') }}"></script>
@endsection