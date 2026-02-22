@extends('layouts.auth')

@section('title', __('Password Recovery') . ' - TalentsLab')

@section('content')
<h1>{{ __('Password Recovery') }}</h1>

@if(!session('status'))
<p>{{ __('Enter your email address and we will send you a password reset link') }}</p>
@endif

@session('status')
    <div class="success-message">
        {{ $value }}
    </div>

    <div class="spam-notice">
        <p><strong>{{ __('Did not find the email?') }}</strong> {{ __('Check your SPAM or Junk folder - sometimes emails end up there automatically.') }}</p>
    </div>
@endsession

<!-- Validation Errors -->
@if ($errors->any())
    <div class="error-message">
        @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

<!-- Forgot Password Form -->
<form method="POST" action="{{ route('password.email') }}">
    @csrf

    @if(!session('status'))
    <div class="form-group">
        <input
            type="email"
            id="email"
            name="email"
            class="form-input"
            placeholder="{{ __('Email address') }}"
            value="{{ old('email') }}"
            required
            autofocus
            autocomplete="username"
        >
    </div>
    @else
    <input type="hidden" name="email" value="{{ session('reset_email') }}">
    @endif

    @if(!session('status'))
    <button type="submit" class="btn-auth">
        {{ __('Send password reset link') }}
    </button>
    @else
    <div class="resend-section">
        <p class="resend-text">{{ __('Want to resend the link?') }}</p>
        <button type="submit" class="btn-auth btn-secondary">
            {{ __('Resend') }}
        </button>
    </div>
    @endif
</form>

<div class="auth-footer">
    <a href="{{ route('login') }}">← {{ __('Back to login') }}</a>
</div>
@endsection
