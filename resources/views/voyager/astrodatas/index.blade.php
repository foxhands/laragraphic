<?php
@extends('astro::layouts.master')

@section('content')
    <p>Первичный знак: {{ $primary_zodiac_sign }}</p>
    @isset($secondary_zodiac_sign)
        <p>Вторичный знак:  {{$secondary_zodiac_sign}}</p>
    @endisset
@endsection
