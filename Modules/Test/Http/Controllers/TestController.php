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
            1 => "Aries", // ����
            2 => "Taurus", // �����
            3 => "Gemini", // ��������
            4 => "Cancer", // ���
            5 => "Leo", // ���
            6 => "Virgo", // ����
            7 => "Libra", // ����
            8 => "Scorpio", // ��������
            9 => "Sagittarius", // �������
            10 => "Capricorn", // �������
            11 => "Aquarius", // �������
            12 => "Pisces" // ����
        );

        dd($zodiacSigns);

    }

}
