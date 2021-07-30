@extends('layout-login')

@section('content')
<div id="component" class="box-login bg-white border-left p-3 text-center pt-5">
    <form id="login" class="w-100" v-on:submit.prevent="submit" data-vv-scope="login" novalidate>
        <h1 class="mt-0 mb-4 text-center h2">Login</h1>
        <div v-cloak class="alert alert-danger mb-4 text-left" v-if="invalid" >
            {{ __('Invalid username or password.') }}
            <button type="button" class="close" v-on:click="dismiss()" aria-label="Close">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <label for="username" class="sr-only">{{ __('Email address') }}</label>
        <input type="text" id="username" name="username" class="form-control text-center" placeholder="{{ __('Username or Email address') }}" required autofocus
               v-validate="'required'"
               v-model="login.username"
               v-bind:class="{ 'is-invalid': errors.has('login.username') }">
        <label for="inputPassword" class="sr-only">{{ __('Password') }}</label>
        <input type="password" id="password" name="password" class="form-control text-center" placeholder="{{ __('Password') }}" required
               v-validate="'required'"
               v-model="login.password"
               v-bind:class="{ 'is-invalid': errors.has('login.password') }">
        <div class="custom-control custom-checkbox mt-2">
            <input type="checkbox" class="custom-control-input" name="remember" id="remember" value="1">
            <label class="custom-control-label" for="remember">{{ __('Remember me') }}</label>
        </div>
        <p class="mb-0 mt-3">
            <button class="btn btn-lg btn-primary btn-block" type="submit">{{ __('Sign in') }}</button>
        </p>
        <p class="mb-0 mt-5">
            {{ __("Don't have an account?") }} <a href="{{ route('register.index') }}">{{ __('Sign Up') }}</a><br>
            <a class="btn btn-link" href="{{ route('password.request') }}">{{ __('Forgot your password?') }}</a>
        </p>
        <p class="mb-0 mt-5">
            <a href="<?php echo route('home'); ?>">&xlarr; {{ __('Back to Home Page') }}</a>
        </p>
    </form>
</div>
@endsection

@section('javascript')
    <script src="{{ asset('/js/login.js') }}"></script>
@endsection