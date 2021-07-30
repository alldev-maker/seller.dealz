@extends('layout-guest')

@section('content')
<div id="component" class="container">
    <div class="row justify-content-center">
        <div class="col-12">
            <form v-on:submit.prevent="submit" data-vv-scope="testtaker" class="py-4">
                <div class="row text-center">
                    <div class="col">
                        <h1 class="">Registration</h1>
                        <p>Please fill out the required fields.</p>
                    </div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="card mb-3">
                            <div class="card-header"><h2 class="h6 m-0 font-weight-bold">Credentials</h2></div>
                            <div class="card-body">
                                <div class="form-group form-row">
                                    <label for="username" class="col-md-3 col-form-label">User Name <sup class="text-danger"><i class="fas fa-sm fa-asterisk"></i></sup></label>
                                    <div class="col">
                                        <input type="text" id="username" name="testtaker.user.name" class="form-control"
                                               v-model="testtaker.user.name"
                                               v-validate="'required|min:3|unique:name'"
                                               v-bind:class="{ 'is-invalid': errors.has('testtaker.user.name') }"
                                        >
                                        <div v-for="error in errors.collect('testtaker.user.name')" class="invalid-feedback">@{{ error }}</div>
                                        <div class="form-text small text-muted">Your user name. Used for login.</div>
                                    </div>

                                </div>
                                <div class="form-group form-row">
                                    <label for="password" class="col-md-3 col-form-label">Password <sup class="text-danger"><i class="fas fa-sm fa-asterisk"></i></sup></label>
                                    <div class="col">
                                        <input type="password" id="password" name="testtaker.user.password" class="form-control"
                                               ref="password"
                                               v-model="testtaker.user.password"
                                               v-validate="'required|min:10'"
                                               v-bind:class="{ 'is-invalid': errors.has('testtaker.user.password') }"
                                        >
                                        <div v-for="error in errors.collect('testtaker.user.password')" class="invalid-feedback">@{{ error }}</div>
                                        <div class="form-text small text-muted">Your password. Used for login.</div>
                                    </div>
                                </div>
                                <div class="form-group form-row">
                                    <label for="password_confirm" class="col-md-3 col-form-label">Confirm Password <sup class="text-danger"><i class="fas fa-sm fa-asterisk"></i></sup></label>
                                    <div class="col">
                                        <input type="password" id="password_confirm" name="testtaker.user.password_confirm" class="form-control"
                                               v-model="testtaker.user.password_confirm"
                                               v-validate="'required|min:10|confirmed:password'"
                                               v-bind:class="{ 'is-invalid': errors.has('testtaker.user.password_confirm') }"
                                        >
                                        <div v-for="error in errors.collect('testtaker.user.password_confirm')" class="invalid-feedback">@{{ error }}</div>
                                        <div class="form-text small text-muted">Type your password again.</div>
                                    </div>
                                </div>
                                <div class="form-group form-row mb-0">
                                    <label for="name_nice" class="col-md-3 col-form-label">Email <sup class="text-danger"><i class="fas fa-sm fa-asterisk"></i></sup></label>
                                    <div class="col">
                                        <input type="email" id="email" name="email" class="form-control"
                                               v-model="testtaker.email"
                                               v-validate="'email|required|unique:email'"
                                               v-bind:class="{ 'is-invalid': errors.has('testtaker.email') }"
                                        >
                                        <div v-for="error in errors.collect('testtaker.email')" class="invalid-feedback">@{{ error }}</div>
                                        <div class="form-text small text-muted">Your email address.</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-3">
                            <div class="card-header"><h2 class="h6 m-0 font-weight-bold">General Information</h2></div>
                            <div class="card-body">
                                <div class="form-group form-row">
                                    <label for="given_name" class="col-md-3 col-form-label">Given Name <sup class="text-danger"><i class="fas fa-sm fa-asterisk"></i></sup></label>
                                    <div class="col">
                                        <input type="text" id="given_name" name="given_name" class="form-control"
                                               v-model="testtaker.given_name"
                                               v-validate="'required'"
                                               v-bind:class="{ 'is-invalid': errors.has('testtaker.given_name') }"
                                        >
                                        <div v-for="error in errors.collect('testtaker.given_name')" class="invalid-feedback">@{{ error }}</div>
                                        <div class="form-text small text-muted">Your given name or first name.</div>
                                    </div>
                                </div>
                                <div class="form-group form-row">
                                    <label for="family_name" class="col-md-3 col-form-label">
                                        Family Name <sup class="text-danger"><i class="fas fa-sm fa-asterisk"></i></sup>
                                    </label>
                                    <div class="col">
                                        <input type="text" id="family_name" name="family_name" class="form-control"
                                               v-model="testtaker.family_name"
                                               v-validate="'required'"
                                               v-bind:class="{ 'is-invalid': errors.has('testtaker.family_name') }"
                                        >
                                        <div v-for="error in errors.collect('testtaker.family_name')" class="invalid-feedback">@{{ error }}</div>
                                        <div class="form-text small text-muted">Your family name or last name.</div>
                                    </div>
                                </div>
                                <div class="form-group form-row">
                                    <label for="suffix" class="col-md-3 col-form-label">Suffix</label>
                                    <div class="col-md-3">
                                        <input type="text" id="suffix" name="suffix" class="form-control" v-model="testtaker.suffix">
                                        <div class="form-text small text-muted">Examples: Jr., Sr., III</div>
                                    </div>
                                </div>
                                <div class="form-group form-row">
                                    <label for="nick" class="col-md-3 col-form-label">Nickname</label>
                                    <div class="col">
                                        <input type="text" id="nick" name="nick" class="form-control" v-model="testtaker.nickname">
                                        <div class="form-text small text-muted">Your nickname.</div>
                                    </div>
                                </div>
                                <div class="form-group form-row mb-0">
                                    <label for="name_display" class="col-md-3 col-form-label">Display Name</label>
                                    <div class="col">
                                        <input type="text" id="name_display" name="name_display" class="form-control" v-model="testtaker.nice_name">
                                        <div class="form-text small text-muted">Your display name. Used for mail messaging and notifications.</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-3">
                            <div class="card-header"><h2 class="h6 m-0 font-weight-bold">Contact Information</h2></div>
                            <div class="card-body">
                                <div class="form-group form-row">
                                    <label for="phone_mobile" class="col-md-3 col-form-label">Mobile Phone No. </label>
                                    <div class="col">
                                        <input type="text" id="phone_mobile" name="phone_mobile" class="form-control"
                                               v-model="testtaker.phone_mobile"
                                        >
                                        <div v-for="error in errors.collect('testtaker.phone_mobile')" class="invalid-feedback">@{{ error }}</div>
                                    </div>
                                </div>
                                <div class="form-group form-row mb-0">
                                    <label for="phone_landline" class="col-md-3 col-form-label">Landline Phone No.</label>
                                    <div class="col">
                                        <input type="text" id="phone_landline" name="phone_landline" class="form-control" v-model="testtaker.phone_landline">
                                        <div v-for="error in errors.collect('testtaker.phone_landline')" class="invalid-feedback">@{{ error }}</div>
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
                                        <input type="text" id="school" name="testtaker.school" class="form-control"
                                               v-model="testtaker.school"
                                               v-validate="'required'"
                                               v-bind:class="{ 'is-invalid': errors.has('testtaker.school') }">
                                        <div v-for="error in errors.collect('testtaker.school')" class="invalid-feedback">@{{ error }}</div>
                                        <div class="form-text small text-muted">Your school, college, university, or institute.</div>
                                    </div>
                                </div>
                                <div class="form-group form-row">
                                    <label for="address" class="col-md-3 col-form-label">Address <sup class="text-danger"><i class="fas fa-sm fa-asterisk"></i></sup></label>
                                    <div class="col">
                                        <input type="text" id="address" name="testtaker.address" class="form-control"
                                               v-model="testtaker.address"
                                               v-validate="'required'"
                                               v-bind:class="{ 'is-invalid': errors.has('testtaker.address') }">
                                        <div v-for="error in errors.collect('testtaker.address')" class="invalid-feedback">@{{ error }}</div>
                                        <div class="form-text small text-muted">Your address. Usually number and street.</div>
                                    </div>
                                </div>
                                <div class="form-group form-row">
                                    <label for="locality" class="col-md-3 col-form-label">City/Town <sup class="text-danger"><i class="fas fa-sm fa-asterisk"></i></sup></label>
                                    <div class="col">
                                        <input type="text" id="locality" name="testtaker.locality" class="form-control"
                                               v-model="testtaker.locality"
                                               v-validate="'required'"
                                               v-bind:class="{ 'is-invalid': errors.has('testtaker.locality') }">
                                        <div v-for="error in errors.collect('testtaker.locality')" class="invalid-feedback">@{{ error }}</div>
                                        <div class="form-text small text-muted">Your city or town.</div>
                                    </div>
                                </div>
                                <div class="form-group form-row">
                                    <label for="county" class="col-md-3 col-form-label">County <sup class="text-danger"><i class="fas fa-sm fa-asterisk"></i></sup></label>
                                    <div class="col">
                                        <input type="text" id="county" name="testtaker.county" class="form-control"
                                               v-model="testtaker.county"
                                               v-validate="'required'"
                                               v-bind:class="{ 'is-invalid': errors.has('testtaker.county') }">
                                        <div v-for="error in errors.collect('testtaker.county')" class="invalid-feedback">@{{ error }}</div>
                                        <div class="form-text small text-muted">Your county, borough (Alaska), or parish (Louisiana).</div>
                                    </div>
                                </div>

                                <div class="form-group form-row">
                                    <label for="state" class="col-md-3 col-form-label">State <sup class="text-danger"><i class="fas fa-sm fa-asterisk"></i></sup></label>
                                    <div class="col">
                                        <input type="text" id="state" name="testtaker.state" class="form-control"
                                               v-model="testtaker.state"
                                               v-validate="'required'"
                                               v-bind:class="{ 'is-invalid': errors.has('testtaker.state') }">
                                        <div v-for="error in errors.collect('testtaker.state')" class="invalid-feedback">@{{ error }}</div>
                                        <div class="form-text small text-muted">Your state.</div>
                                    </div>
                                </div>

                                <div class="form-group form-row">
                                    <label for="country" class="col-md-3 col-form-label">Country/Territory <sup class="text-danger"><i class="fas fa-sm fa-asterisk"></i></sup></label>
                                    <div class="col">
                                        <v-select name="testtaker.country" label="name_common" v-model="testtaker.country"
                                                  v-bind:clearable="false" v-bind:options="countries.items" v-validate="'required'"
                                                  v-bind:class="{ 'is-invalid': errors.has('testtaker.country') }">
                                        </v-select>
                                        <div v-for="error in errors.collect('testtaker.country')" class="invalid-feedback">@{{ error }}</div>
                                        <div class="form-text small text-muted">Select the country first, then province, and then city/town.</div>
                                    </div>
                                </div>

                                <div class="form-group form-row mb-0">
                                    <label for="postal_code" class="col-md-3 col-form-label">Postal Code</label>
                                    <div class="col">
                                        <input type="text" id="postal_code" name="postal_code" class="form-control" v-model="testtaker.postal_code">
                                        <div class="form-text small text-muted">Your postal/ZIP code.</div>
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
                        </p>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script src="{{ asset('js/components/auth/register.js') }}"></script>
@endsection
