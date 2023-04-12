@extends('lifeexpert::layouts.master')

@section('content')
    <form method="POST" action="{{ route('life.form') }}">
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
        @if($name !== 'Test')
        <h1>{{ $name }}</h1>
        <h3>{{ $day }}/{{ $month }}/{{ $year }}</h3>
    <hr>
        @foreach($elements as $key => $element)
            <p>Стихия: {{ $key }} Значения: {{ round($element * 100, 1) }} %</p>
            <hr>

        @endforeach
        @foreach($strategies as $key => $strategy)
            <p>Стратегия: {{ $key }} Значения: {{ round($strategy * 100, 1) }} %</p>

        @endforeach
        @endif
@endsection
