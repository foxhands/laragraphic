@extends('astro::layouts.master')

@section('content')
    <form method="POST" action="{{ route('astro.form') }}">
        @csrf
        <label for="name">Имя:</label>
        <input type="text" name="name" id="name" required>
        <br>
        <label for="name">Число:</label>
        <input type="text" name="day" id="day" required>
        <br>
        <label for="name">Месяц:</label>
        <input type="text" name="month" id="month" required>
        <br>
        <label for="name">Год:</label>
        <input type="text" name="year" id="year" required>
        <br>
        <label for="name">Часов:</label>
        <input type="text" name="hour" id="hour" required>
        <br>
        <label for="name">Минут:</label>
        <input type="text" name="minute" id="minute" required>
        <br>
        <button type="submit">Отправить</button>
    </form>
    @isset($primary_zodiac_sign)
        <p>Первичный знак: {{ $primary_zodiac_sign }}</p>
    @endisset
    @isset($secondary_zodiac_sign)
        <p>Вторичный знак:  {{$secondary_zodiac_sign}}</p>
    @endisset
@endsection
