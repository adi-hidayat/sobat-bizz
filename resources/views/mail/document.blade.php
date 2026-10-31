<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <title>{{ $number }}</title>
</head>
<body style="font-family: Arial, sans-serif; font-size: 14px; color: #1f2937;">
    <p>{{ __('documents.mail.greeting') }}</p>

    <p>{!! nl2br(e($message)) !!}</p>

    <p>
        {{ __('documents.mail.attachment_before') }}
        <strong>{{ $number }}</strong>
        {{ __('documents.mail.attachment_after') }}
    </p>

    <p>{{ __('documents.mail.thanks') }}</p>

    <p>
        {{ __('documents.mail.regards') }}<br>
        <strong>{{ config('app.name') }}</strong>
    </p>
</body>
</html>
