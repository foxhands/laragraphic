<?php

namespace Modules\Test\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class TestController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {



        $zodiacSigns = array(
            1 => "Aries", // Овен
            2 => "Taurus", // Телец
            3 => "Gemini", // Близнецы
            4 => "Cancer", // Рак
            5 => "Leo", // Лев
            6 => "Virgo", // Дева
            7 => "Libra", // Весы
            8 => "Scorpio", // Скорпион
            9 => "Sagittarius", // Стрелец
            10 => "Capricorn", // Козерог
            11 => "Aquarius", // Водолей
            12 => "Pisces" // Рыбы
        );

        dd($zodiacSigns);

    }

}
