@extends('master')
@section('content')
    {!! Form::open('myFormId')->requestObject(FormFactoryTests\Browser\Requests\CaptchaTestRequest::class)->action('/captcha-post') !!}
    {!! Form::submit('submit') !!}
    {!! Form::close() !!}
@endsection