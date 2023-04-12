<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Routing\Controller;

class EgoController extends Controller
{
    /**
     * Получает название уровня мужчины из массива данных.
     *
     * @param array $data Массив данных.
     *
     * @return string Название найденного уровня или "None", если уровень не найден.
     */
    public function egocentrismCalculator(array $data): string
    {
        $egos = collect($data)->map(function ($person) {
            $primarySign = $person['zodiac']['primary'];
            $secondarySign = $person['zodiac']['secondary'];
            $getLevels = $this->getLevels($person);
            return $this->calculateValue($primarySign, $secondarySign, $getLevels);
        });
        return $egos->implode('');
    }

    function calculateValue(int $primary, ?int $secondary, array $levels): string
    {
        $primaryStatus = $this->hasPrimary($levels, $primary);
        return $this->getValue($primary, $secondary, $primaryStatus, $levels);
    }

    private function hasPrimary($levels,$primary){
        // Ищем первичный знак зодиака на главном уровне
        if (isset($levels['mainLevel'])) {
            if ($levels["mainLevel"]["sun"]["zodiac"] == $primary &&
                ($levels["mainLevel"]["sun"]["status"] == "rise" || $levels["mainLevel"]["sun"]["status"] == "abode")) {
                 return $levels["mainLevel"]["sun"]["status"];
            }
        }
        return null;
    }

    function getValue($primary, $secondary, $primaryStatus, $levels): string
    {
        $getFirstLevelValue = $this->checkCondition1($primary, $secondary, $primaryStatus, $levels);
        if (!empty($getFirstLevelValue)) {
            return $getFirstLevelValue;
        }

        $getSecondLevelValue = $this->checkCondition2($primary, $secondary, $primaryStatus, $levels);
        if (!empty($getSecondLevelValue)) {
            return $getSecondLevelValue;
        }

        $getSixthLevelValue = $this->checkCondition6($levels, $secondary);
        if (!empty($getSixthLevelValue)) {
            return $getSixthLevelValue;
        }

        $getThirdLevelValue = $this->checkCondition3($primary, $secondary, $primaryStatus, $levels);
        if (!empty($getThirdLevelValue)) {
            return $getThirdLevelValue;
        }

        $getFifthLevelValue = $this->checkCondition5($primary, $secondary, $primaryStatus, $levels);
        if (!empty($getFifthLevelValue)) {
            return $getFifthLevelValue;
        }

        $getFourthLevelValue = $this->checkCondition4($primary, $secondary, $levels);
        if (!empty($getFourthLevelValue)) {
            return $getFourthLevelValue;
        }

        return '';

    }


    private function checkCondition1($primary, $secondary, $primaryStatus, $levels): string
    {
        $hasPrimary = $primaryStatus == 'rise' || $primaryStatus == 'abode';
        $otherZodiacs = array_map(function ($planetData) {
            return $planetData['zodiac'];
        }, $levels['secondaryLevel']);
        $hasSecondary = in_array($secondary, $otherZodiacs) || in_array($primary, $otherZodiacs);

        if ($hasPrimary && !$hasSecondary && count($levels['secondaryLevel']) == 0) {
            return '4A';
        } elseif ($hasPrimary && $hasSecondary && count($levels['secondaryLevel']) == 0) {
            return '3A';
        } else {
            return '';
        }
    }

    private function checkCondition2($primary, $secondary, $primaryStatus, $levels): string
    {
        $hasPrimary = $primaryStatus == 'rise' || $primaryStatus == 'abode';
        $otherZodiacs = array_map(function ($planetData) {
            return $planetData['zodiac'];
        }, $levels['secondaryLevel']);
        $hasSecondary = in_array($secondary, $otherZodiacs) || in_array($primary, $otherZodiacs);

        if (count($levels['secondaryLevel']) == 1 && $levels['secondaryLevel'][0]['status'] != 'none') {
            return '5A';
        } else {
            return '';
        }
    }

    private function checkCondition3($primary, $secondary, $primaryStatus, $levels): string
    {
        $hasPrimary = $primaryStatus == 'rise' || $primaryStatus == 'abode';
        $otherZodiacs = array_map(function ($planetData) {
            return $planetData['zodiac'];
        }, $levels['secondaryLevel']);
        $hasSecondary = in_array($secondary, $otherZodiacs) || in_array($primary, $otherZodiacs);

        if ($hasPrimary && $hasSecondary) {
            foreach ($levels['secondaryLevel'] as $planetData) {
                if ($planetData['status'] == 'rise' || $planetData['status'] == 'abode') {
                    return '4A';
                }
            }
            return '3A';
        }

        return '';
    }

    private function checkCondition4($primary, $secondary, $levels): ?string
    {
        $otherZodiacs = array_map(function ($planetData) {
            return $planetData['zodiac'];
        }, $levels['secondaryLevel']);
        $hasSecondary = in_array($secondary, $otherZodiacs) || in_array($primary, $otherZodiacs);

        if (!$hasSecondary && count($levels['secondaryLevel']) == 0) {
            return '2A';
        } else {
            return null;
        }
    }

    private function checkCondition5($primary, $secondary, $primaryStatus, $levels) {
        $otherZodiacs = array_map(function ($planetData) {
            return $planetData['zodiac'];
        }, $levels['secondaryLevel']);

        $hasSecondaryAbodeOrRise = false;
        foreach ($levels['secondaryLevel'] as $planetData) {
            if (($planetData['status'] == 'rise' || $planetData['status'] == 'abode') && $planetData['zodiac'] == $secondary) {
                $hasSecondaryAbodeOrRise = true;
                break;
            }
        }

        if ($hasSecondaryAbodeOrRise) {
            return '1A';
        } else {
            return $this->getValueForNoAbodeOrRise($primary, $secondary, $primaryStatus, $levels);
        }
    }

    private function checkCondition6($levels, $secondary): string
    {
        $abodeOrRiseCount = 0;
        foreach ($levels as $level) {
            foreach ($level as $planetData) {
                if (($planetData['status'] == 'abode' || $planetData['status'] == 'rise') &&
                    $planetData['zodiac'] == $secondary ) {
                    $abodeOrRiseCount++;
                }
            }
        }
        if ($abodeOrRiseCount >= 2) {
            return '6A';
        } else {
            return '';
        }
    }


    private function getValueForNoAbodeOrRise($primaryZodiacSign, $secondaryZodiacSigns): string
    {
        if (($primaryZodiacSign == "abode" || $primaryZodiacSign == "rise") && empty($secondaryZodiacSigns)) {
            return "4A";
        } elseif (!empty($secondaryZodiacSigns)) {
            return "3A";
        } else {
            return "2A";
        }
    }








    /**
     * Получает данные по уровням вторичных планет мужчины.
     *
     * @param array $zodiacData Массив данных мужчины.
     *
     * @return array Данные по уровням вторичных планет.
     */
    private function getLevels(array $zodiacData): array
    {
        // Получаем данные планет из массива $zodiacData
        $levelSign = $zodiacData['cosmo']['planets'];

        // Создаем массивы для хранения данных по уровням планет
        $mainLevel = [];
        $secondaryLevel = [];

        // Получаем данные для каждой планеты, кроме Солнца
        foreach ($levelSign as $key => $item) {
            if ($key === 'sun') {
                $mainLevel[$key] = [
                    'zodiac' => $item[1],
                    'status' => !empty($item[2]) ? $item[2][0] : null
                ];
            } else{
                $secondaryLevel[$key] = [
                    'zodiac' => $item[1],
                    'status' => !empty($item[2]) ? $item[2][0] : null
                ];
            }
        }

        // Возвращаем массив данных по уровням планет
        return [
            'mainLevel' => $mainLevel,
            'secondaryLevel' => $secondaryLevel,
        ];
    }
}
