@extends('layouts.auth')

@section('title', __('Email Verification') . ' - Divergents')

@section('content')
<h1>{{ __('Email Verification') }}</h1>
<p>{{ __('Before continuing, please verify your email address by clicking on the link we just sent you. If you did not receive the email, we will gladly send you another.') }}</p>

<div class="spam-notice">
    <p><strong>{{ __('Did not find the email?') }}</strong> {{ __('Check your SPAM or Junk folder - sometimes emails end up there automatically.') }}</p>
</div>

@if (session('status') == 'verification-link-sent')
    <div class="success-message">
        {{ __('We sent a new link to the email specified in your profile.') }}
    </div>
@endif

<!-- Resend Verification Form -->
<form method="POST" action="{{ route('verification.send') }}">
    @csrf
    <button type="submit" class="btn-auth">
        {{ __('Resend email') }}
    </button>
</form>

<div class="auth-footer">
    <a href="{{ route('profile.show') }}">{{ __('Edit Profile') }}</a>
    <form method="POST" action="{{ route('logout') }}" class="inline">
        @csrf
        <button type="submit" class="logout-link">
            {{ __('Logout') }}
        </button>
    </form>
</div>
@endsection
