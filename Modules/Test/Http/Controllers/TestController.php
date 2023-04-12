<?php

namespace Modules\Test\Http\Controllers;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;


class TestController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return void
     * @throws GuzzleException
     */
    public function index()
    {


// Создаем клиент Guzzle
        $client = new Client();

// Пример строки со списком городов
        $options = '<option value="1">Абаза</option><option value="2">Абакан</option><option value="3">Абдулино</option>';

// Разбиваем строку на массив элементов по тегу </option>
        $optionArr = explode('</option>', $options);

// Обходим каждый элемент
        foreach ($optionArr as $option) {
            // Находим значение и текст элемента
            preg_match('/value="(.*)">(.*)/', $option, $matches);
            $value = $matches[1];
            $text = $matches[2];

            // Делаем необходимые действия с полученными данными
            $cityId = $value;
            $birthday = '1995';
            $url = 'http://cosmos.loc/api/proxy?url=https://astro-online.ru/ajax/ajax.city.php?city=' . $cityId . '&birthyear=' . $birthday;

            // Отправляем запрос по полученному URL с использованием Guzzle
            $response = $client->request('GET', $url)->getBody()->getContents();

            // Используем регулярные выражения для поиска значений N, E, GMT и DATE в ответе
            preg_match('/N:(.*),/', $response, $nMatch);
            $N = trim($nMatch[1]);

            preg_match('/E:(.*),/', $response, $eMatch);
            $E = trim($eMatch[1]);

            preg_match('/GMT:(.*),/', $response, $gmtMatch);
            $GMT = trim($gmtMatch[1]);

dd($response,$N,$E,$GMT,$cityId,$text);
            // Получаем координаты города
            $N = $data['N'];
            $E = $data['E'];

            // Сохраняем результат в базу данных
        }

    }

}
