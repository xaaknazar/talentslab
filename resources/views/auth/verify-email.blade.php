@extends('layouts.auth')

@section('title', 'Подтверждение email - Divergents')

@section('content')
<h1>Подтверждение email</h1>
<p>Перед продолжением, пожалуйста, подтвердите ваш адрес электронной почты, нажав на ссылку, которую мы только что отправили вам. Если вы не получили письмо, мы с удовольствием отправим вам другое.</p>

<div class="spam-notice">
    <p><strong>Не нашли письмо?</strong> Проверьте папку "СПАМ" или "Нежелательная почта" - иногда письма попадают туда автоматически.</p>
</div>

@if (session('status') == 'verification-link-sent')
    <div class="success-message">
        Мы отправили новую ссылку на почту, указанную в профиле.
    </div>
@endif

<!-- Resend Verification Form -->
<form method="POST" action="{{ route('verification.send') }}">
    @csrf
    <button type="submit" class="btn-auth">
        Отправить письмо повторно
    </button>
</form>

<div class="auth-footer">x`
    <a href="{{ route('profile.show') }}">Редактировать профиль</a>
    <form method="POST" action="{{ route('logout') }}" class="inline">
        @csrf
        <button type="submit" class="logout-link">
            Выйти
        </button>
    </form>
</div>
@endsection
