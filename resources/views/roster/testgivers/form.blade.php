@extends('layout')

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('roster.index')  }}">Roster</a></li>
            <li class="breadcrumb-item">Test Givers</li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo $testgiver->id > 0 ? 'Edit' : 'Add New'; ?></li>
        </ol>
    </nav>
    <div class="container-fluid px-3">
        <div id="component" class="row">
            <div class="col">
                @component('elements.spinner') @endcomponent
                <div class="content" v-cloak>
                    <form v-on:submit.prevent="submit" data-vv-scope="testgiver">
                        <div class="row justify-content-center">
                            <div class="col-md-8">
                                <div class="card mb-3">
                                    <div class="card-header"><h2 class="h6 m-0 font-weight-bold">General Information</h2></div>
                                    <div class="card-body">
                                        <div class="form-group form-row">
                                            <label for="given_name" class="col-md-3 col-form-label">Given Name <sup class="text-danger"><i class="fas fa-sm fa-asterisk"></i></sup></label>
                                            <div class="col">
                                                <input type="text" id="given_name" name="given_name" class="form-control"
                                                       v-model="testgiver.given_name"
                                                       v-validate="'required'"
                                                       v-bind:class="{ 'is-invalid': errors.has('testgiver.given_name') }"
                                                >
                                                <div v-for="error in errors.collect('testgiver.given_name')" class="invalid-feedback">@{{ error }}</div>
                                                <div class="form-text small text-muted">Member's given name or first name.</div>
                                            </div>
                                        </div>
                                        <div class="form-group form-row">
                                            <label for="family_name" class="col-md-3 col-form-label">
                                                Family Name <sup class="text-danger"><i class="fas fa-sm fa-asterisk"></i></sup>
                                            </label>
                                            <div class="col">
                                                <input type="text" id="family_name" name="family_name" class="form-control"
                                                       v-model="testgiver.family_name"
                                                       v-validate="'required'"
                                                       v-bind:class="{ 'is-invalid': errors.has('testgiver.family_name') }"
                                                >
                                                <div v-for="error in errors.collect('testgiver.family_name')" class="invalid-feedback">@{{ error }}</div>
                                                <div class="form-text small text-muted">Member's family name or last name.</div>
                                            </div>
                                        </div>
                                        <div class="form-group form-row">
                                            <label for="suffix" class="col-md-3 col-form-label">Suffix</label>
                                            <div class="col-md-3">
                                                <input type="text" id="suffix" name="suffix" class="form-control" v-model="testgiver.suffix">
                                                <div class="form-text small text-muted">Examples: Jr., Sr., III</div>
                                            </div>
                                        </div>
                                        <div class="form-group form-row">
                                            <label for="nick" class="col-md-3 col-form-label">Nickname</label>
                                            <div class="col">
                                                <input type="text" id="nick" name="nick" class="form-control" v-model="testgiver.nickname">
                                                <div class="form-text small text-muted">Member's nickname.</div>
                                            </div>
                                        </div>
                                        <div class="form-group form-row mb-0">
                                            <label for="name_display" class="col-md-3 col-form-label">Display Name</label>
                                            <div class="col">
                                                <input type="text" id="name_display" name="nick" class="form-control" v-model="testgiver.nice_name">
                                                <div class="form-text small text-muted">Member's display name. Used for mail messaging and notifications.</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="card mb-3">
                                    <div class="card-header"><h2 class="h6 m-0 font-weight-bold">Credentials</h2></div>
                                    <div class="card-body">
                                        <div class="form-group form-row">
                                            <label for="username" class="col-md-3 col-form-label">User Name <sup class="text-danger"><i class="fas fa-sm fa-asterisk"></i></sup></label>
                                            <div class="col">
                                                <input type="text" id="username" name="testgiver.user.name" class="form-control"
                                                       v-model="testgiver.user.name"
                                                       v-validate="'required|min:3|unique:name'"
                                                       v-bind:class="{ 'is-invalid': errors.has('testgiver.user.name') }"
                                                >
                                                <div v-for="error in errors.collect('testgiver.user.name')" class="invalid-feedback">@{{ error }}</div>
                                                <div class="form-text small text-muted">Member's user name. Used for login.</div>
                                            </div>

                                        </div>
                                        <div class="form-group form-row mb-0">
                                            <label for="password" class="col-md-3 col-form-label">Password <sup v-if="testgiver.id === ''" class="text-danger"><i class="fas fa-sm fa-asterisk"></i></sup></label>
                                            <div class="col">
                                                <input type="text" id="password" name="testgiver.user.password" class="form-control"
                                                       v-model="testgiver.user.password"
                                                       v-validate="testgiver.id === 0 ? 'required|min:10' : ''"
                                                       v-bind:class="{ 'is-invalid': errors.has('testgiver.user.password') }"
                                                >
                                                <div v-for="error in errors.collect('testgiver.user.password')" class="invalid-feedback">@{{ error }}</div>
                                                <div class="form-text small text-muted">Member's password. Used for login.</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card mb-3">
                                    <div class="card-header"><h2 class="h6 m-0 font-weight-bold">Contact Information</h2></div>
                                    <div class="card-body">
                                        <div class="form-group form-row">
                                            <label for="name_nice" class="col-md-3 col-form-label">Email <sup class="text-danger"><i class="fas fa-sm fa-asterisk"></i></sup></label>
                                            <div class="col">
                                                <input type="email" id="email" name="email" class="form-control"
                                                       v-model="testgiver.email"
                                                       v-validate="'email|required|unique:email'"
                                                       v-bind:class="{ 'is-invalid': errors.has('testgiver.email') }"
                                                >
                                                <div v-for="error in errors.collect('testgiver.email')" class="invalid-feedback">@{{ error }}</div>
                                            </div>
                                        </div>
                                        <div class="form-group form-row">
                                            <label for="phone_mobile" class="col-md-3 col-form-label">Mobile Phone No. </label>
                                            <div class="col">
                                                <input type="text" id="phone_mobile" name="phone_mobile" class="form-control"
                                                       v-model="testgiver.phone_mobile"
                                                >
                                                <div v-for="error in errors.collect('testgiver.phone_mobile')" class="invalid-feedback">@{{ error }}</div>
                                            </div>
                                        </div>
                                        <div class="form-group form-row mb-0">
                                            <label for="phone_landline" class="col-md-3 col-form-label">Landline Phone No.</label>
                                            <div class="col">
                                                <input type="text" id="phone_landline" name="phone_landline" class="form-control" v-model="testgiver.phone_landline">
                                                <div v-for="error in errors.collect('testgiver.phone_landline')" class="invalid-feedback">@{{ error }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card mb-3">
                                    <div class="card-header"><h2 class="h6 m-0 font-weight-bold">Address</h2></div>
                                    <div class="card-body">
                                        <div class="form-group form-row">
                                            <label for="school" class="col-md-3 col-form-label">School/Institution <sup class="text-danger"><i class="fas fa-sm fa-asterisk"></i></sup></label>
                                            <div class="col">
                                                <input type="text" id="school" name="testgiver.school" class="form-control"
                                                       v-model="testgiver.school"
                                                       v-validate="'required'"
                                                       v-bind:class="{ 'is-invalid': errors.has('testgiver.school') }">
                                                <div v-for="error in errors.collect('testgiver.school')" class="invalid-feedback">@{{ error }}</div>
                                                <div class="form-text small text-muted">Test giver's school, college, university, or institute.</div>
                                            </div>
                                        </div>
                                        <div class="form-group form-row">
                                            <label for="address" class="col-md-3 col-form-label">Address <sup class="text-danger"><i class="fas fa-sm fa-asterisk"></i></sup></label>
                                            <div class="col">
                                                <input type="text" id="address" name="testgiver.address" class="form-control"
                                                       v-model="testgiver.address"
                                                       v-validate="'required'"
                                                       v-bind:class="{ 'is-invalid': errors.has('testgiver.address') }">
                                                <div v-for="error in errors.collect('testgiver.address')" class="invalid-feedback">@{{ error }}</div>
                                                <div class="form-text small text-muted">Test giver's address. Usually number and street.</div>
                                            </div>
                                        </div>
                                        <div class="form-group form-row">
                                            <label for="locality" class="col-md-3 col-form-label">City/Town <sup class="text-danger"><i class="fas fa-sm fa-asterisk"></i></sup></label>
                                            <div class="col">
                                                <input type="text" id="locality" name="testgiver.locality" class="form-control"
                                                       v-model="testgiver.locality"
                                                       v-validate="'required'"
                                                       v-bind:class="{ 'is-invalid': errors.has('testgiver.locality') }">
                                                <div v-for="error in errors.collect('testgiver.locality')" class="invalid-feedback">@{{ error }}</div>
                                                <div class="form-text small text-muted">Test giver's city or town.</div>
                                            </div>
                                        </div>
                                        <div class="form-group form-row">
                                            <label for="county" class="col-md-3 col-form-label">County/Borough/Parish <sup class="text-danger"><i class="fas fa-sm fa-asterisk"></i></sup></label>
                                            <div class="col">
                                                <input type="text" id="county" name="testgiver.county" class="form-control"
                                                       v-model="testgiver.county"
                                                       v-validate="'required'"
                                                       v-bind:class="{ 'is-invalid': errors.has('testgiver.county') }">
                                                <div v-for="error in errors.collect('testgiver.county')" class="invalid-feedback">@{{ error }}</div>
                                                <div class="form-text small text-muted">Test giver's county.</div>
                                            </div>
                                        </div>

                                        <div class="form-group form-row">
                                            <label for="state" class="col-md-3 col-form-label">State <sup class="text-danger"><i class="fas fa-sm fa-asterisk"></i></sup></label>
                                            <div class="col">
                                                <input type="text" id="state" name="testgiver.state" class="form-control"
                                                       v-model="testgiver.state"
                                                       v-validate="'required'"
                                                       v-bind:class="{ 'is-invalid': errors.has('testgiver.state') }">
                                                <div v-for="error in errors.collect('testgiver.state')" class="invalid-feedback">@{{ error }}</div>
                                                <div class="form-text small text-muted">Test giver's state.</div>
                                            </div>
                                        </div>

                                        <div class="form-group form-row">
                                            <label for="country" class="col-md-3 col-form-label">Country/Territory <sup class="text-danger"><i class="fas fa-sm fa-asterisk"></i></sup></label>
                                            <div class="col">
                                                <v-select name="testgiver.country" label="name_common" v-model="testgiver.country"
                                                          v-bind:clearable="false" v-bind:options="countries.items" v-validate="'required'"
                                                          v-bind:class="{ 'is-invalid': errors.has('testgiver.country') }">
                                                </v-select>
                                                <div v-for="error in errors.collect('testgiver.country')" class="invalid-feedback">@{{ error }}</div>
                                                <div class="form-text small text-muted">Select the country first, then province, and then city/town.</div>
                                            </div>
                                        </div>

                                        <div class="form-group form-row mb-0">
                                            <label for="postal_code" class="col-md-3 col-form-label">Postal Code</label>
                                            <div class="col">
                                                <input type="text" id="postal_code" name="postal_code" class="form-control" v-model="testgiver.postal_code">
                                                <div class="form-text small text-muted">Test giver's postal/ZIP code.</div>
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
                                    <a class="btn btn-lg btn-secondary btn-labeled" href="<?php echo route('roster.testgivers.index'); ?>">
                                        <span class="btn-label"><i v-bind:class="{ 'fa-cog fa-spin': submitting, 'fa-chevron-left': !submitting }" class="fas fa-fw"></i></span> Go Back
                                    </a>
                                </p>
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
                                    <p class="mb-0">Are you sure that you will delete the @{{ name.singular }} named <strong>@{{ testgiver.name }}</strong>?</p>
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
    <script>TestGiver = {!! $testgiver->toJson() !!};</script>
    <script src="{{ asset('js/components/roster/testgivers/form.js') }}"></script>
@endsection