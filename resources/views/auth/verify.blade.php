@extends('layout')

@section('content')
<div id="component" class="container">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="border rounded text-center p-5 mt-5">
                <h1 class="mb-4">{{ __('Verify Your Email Address') }}</h1>
                @if (session('resent'))
                    <div class="alert alert-success" role="alert">
                        {{ __('A fresh verification link has been sent to your email address.') }}
                    </div>
                @endif
                {{ __('Before proceeding, please check your email for a verification link.') }}
                {{ __('If you did not receive the email') }},
                <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                    @csrf
                    <button type="submit" class="btn btn-link p-0 m-0 align-baseline">{{ __('click here to request another') }}</button>.
                </form>
                <p><a href="{{ route('logout') }}" class="btn btn-primary">Logout</a></p>
            </div>
        </div>
    </div>
</div>
@endsection
