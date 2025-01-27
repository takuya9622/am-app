@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/form.css') }}" />
@endsection

@section('content')
<x-auth-form :action="Route::is('login') ? 'login' : (Route::is('register') ? 'register' : 'admin.login')" />
@endsection