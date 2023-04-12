<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;

class CoreController extends Controller
{

    public function index(): Renderable
    {
        return view('core::index');
    }


    public function calculate(Request $request): array|string|null
    {
        $ego =  $this->egocentrism($request);
//        $interest = $this->interest($request);
//        $temperam  = $this->temperam($request);

        unset($request);

//'Этап 1:<br>'.$ego.'<hr> Этап 2:<br>'.$interest.'<hr> Этап 3:<br>'.$temperam
        return 'Этап 1:<br>'.$ego;
    }
    private function temperam($request){
        $data = collect(['male', 'female'])
            ->mapWithKeys(fn ($gender) => [$gender => $this->getPersonData($request, $gender)])
            ->toArray();
        return app(TemperamController::class)->calculate($data);
    }


    private function interest($request): ?string
    {
        $data = collect(['male', 'female'])
            ->mapWithKeys(fn ($gender) => [$gender => $this->getPersonData($request, $gender)])
            ->tap(function ($data) {
                $tempCosmo = $data->get('male')['cosmo'];
                $data->get('male')['cosmo'] = $data->get('female')['cosmo'];
                $data->get('female')['cosmo'] = $tempCosmo;
            });

        return app(InterestController::class)->interestCalculator($data->toArray());
    }




    private function egocentrism($request)
    {
        $data = collect(['male', 'female'])
            ->mapWithKeys(fn ($gender) => [$gender => $this->getPersonData($request, $gender)])
            ->toArray();
        return app(EgoController::class)->egocentrismCalculator($data);
    }


     private function getPersonData($request, string $gender): ?array
    {
//        $cacheKey = 'person_data_'. $request->input("{$gender}_name").$request->input("{$gender}_birthday");
//        $cacheTime = 60; // время кэширования в секундах
//
//        // Попытка получения данных из кэша
//        $data = Cache::get($cacheKey);
//        if (!$data) {
            $name = $this->getPersonName($request, $gender);
            $birthDate = $this->getPersonBirthDate($request, $gender);
            $timeDate = $this->getPersonTime($request, $gender);
            [$hours, $minutes] = explode(':', $timeDate);
            [$year, $month, $day] = explode('-', $birthDate);
            $GMT = $this->getPersonGMT($request, $gender);
            $city = $this->getCityData($request, $gender);

            $zodiac = $this->calculateZodiac($name, $day, $month, $year, $hours, $minutes, $city, $GMT);
            $cosmo = $this->calculateCosmo($name, $day, $month, $year, $hours, $minutes, $city);
            // Сохраняем результат выполнения метода в кэш
            $data = compact('name', 'timeDate', 'GMT','city', 'zodiac', 'cosmo');

//            Cache::put($cacheKey, $data, $cacheTime);
//
//        }
        return $data;
    }

    private function getPersonName($request, string $gender): ?string
    {
        return $request->input("{$gender}_name");
    }

    private function getPersonBirthDate($request, string $gender): ?string
    {
        return $request->input("{$gender}_birthday");
    }

    private function calculateZodiac(string $name, int $day, int $month, int $year, $hours, $minutes, $city,$GMT): array
    {
        return app('Modules\Astro\Http\Controllers\AstroController')->calculate($name, $day, $month, $year, $hours, $minutes , $city, $GMT);
    }

    private function calculateCosmo(string $name, int $day, int $month, int $year, $hours, $minutes, $city): array
    {
        return app('Modules\LifeExpert\Http\Controllers\LifeExpertController')->calculate($name, $day, $month, $year, $hours, $minutes , $city);
    }

    private function getCityData($request, string $gender): ?array
    {
        $cityId = $request->input($gender.'_city_id');
        $N = $request->input($gender.'_N');
        $E = $request->input($gender.'_E');

        if (!$cityId || !$N || !$E) {
            return null;
        }

        return [
            'cityId' => $cityId,
            'latitude' => $N,
            'longitude' => $E,
        ];
    }

    private function getPersonTime($request, $gender){
        return $request->input($gender . '_birthtime');
    }

    private function getPersonGMT($request, string $gender)
    {
        return $request->input($gender . '_GMT');
    }
}
