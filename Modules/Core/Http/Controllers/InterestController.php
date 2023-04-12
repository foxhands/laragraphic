<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class InterestController extends Controller
{
    /**
     * Получает название уровня из массива данных.
     *
     * @param array $data Массив данных.
     *
     * @return string Название найденного уровня или "None", если уровень не найден.
     */
    public function interestCalculator(array $data): string
    {
        $egos = collect($data)->map(function ($person) {
            $primarySign = $person['zodiac']['primary'];
            $secondarySign = $person['zodiac']['secondary'];
            $mainLevel = $this->getMainLevel($person);
            $secondaryLevels = $this->getSecondaryLevels($person);
            return $this->calculateLevel($primarySign, $secondarySign, $mainLevel, $secondaryLevels);
        });
        return $egos->implode('');
    }

    /**
     * Вычисляет уровень эгоцентризма на основе данных о знаке зодиака и статусах его планет.
     *
     * @param int $primarySign Данные о первичном знаке зодиака.
     * @param int|null $secondarySign Данные о вторичном знаке зодиака.
     * @param array $mainLevel
     * @param array $secondaryLevels
     * @return string Название уровня эгоцентризма.
     */
    public function calculateLevel(int $primarySign, ?int $secondarySign, array $mainLevel, array $secondaryLevels): string
    {
        $hasAbodeOrExile = false;
        $hasPrimaryAbodeOrExile = false;

        if ($mainLevel && isset($mainLevel['status']) && ($mainLevel['status'] === 'abode' || $mainLevel['status'] === 'exile')) {
            $hasPrimaryAbodeOrExile = true;
        }

        foreach ($secondaryLevels as $planet => $data) {
            if (!array_key_exists($planet, ['sun']) && isset($data['status']) && ($data['status'] === 'abode' || $data['status'] === 'exile')) {
                $hasAbodeOrExile = true;
                break;
            }
        }

        foreach ($secondaryLevels as $item) {
            if ($secondarySign && isset($item['status']) && ($item['status'] === 'abode' || $item['status'] === 'exile')) {
                return '1B';
            } elseif (!$primarySign && !$hasAbodeOrExile && !$hasPrimaryAbodeOrExile) {
                return '2B';
            } elseif ($primarySign && ($item['status'] === 'abode' || $item['status'] === 'exile')) {
                if ($hasAbodeOrExile || $hasPrimaryAbodeOrExile) {
                    return '3B';
                } else {
                    return '4B';
                }
            } elseif ($primarySign && (($item['status'] === 'abode' || $item['status'] === 'exile'))) {
                return '4B';
            } elseif ($primarySign && $secondarySign && ($item['status'] === 'abode' || $item['status'] === 'exile')) {
                return '5B';
            } elseif ($hasAbodeOrExile && count($item) > 1) {
                return '6B';
            }
        }

        return 'None';
    }

    /**
     * Получает знак Зодиака главного
     *
     * @param array $zodiacData Массив данных.
     *
     * @return array Знак Зодиака.
     */
    private function getMainLevel(array $zodiacData): array
    {
        // Получаем данные планет из массива $zodiacData
        $levelSign = $zodiacData['cosmo']['planets'];

        // Получаем уровень планеты Солнце
        $levelMain = $levelSign->sun;

        // Возвращаем массив, содержащий знак зодиака первого
        return [
            'zodiac' => $levelMain[1],
            'status' => $levelMain[2][0] ?? null
        ];
    }

    /**
     * Получает данные по уровням вторичных планет.
     *
     * @param array $zodiacData Массив данных.
     *
     * @return array Данные по уровням вторичных планет.
     */
    private function getSecondaryLevels(array $zodiacData): array
    {
        // Получаем данные планет из массива $zodiacData
        $levelSign = $zodiacData['cosmo']['planets'];

        // Создаем массив для хранения данных по уровням вторичных планет
        $secondaryLevels = [];

        // Получаем данные для каждой планеты, кроме Солнца, узла Ио и узла Юго-Западного узла
        foreach ($levelSign as $key => $item) {
            if (in_array($key, ['sun', 'true node', 'south node'], true)) {
                continue;
            }

            // Получаем знак зодиака и статус планеты
            $text = !empty($item[2]) ? $item[2][0] : null;
            $secondaryLevels[$key] = [
                'zodiac' => $item[1],
                'status' => $text
            ];
        }

        // Возвращаем массив данных по уровням вторичных планет
        return $secondaryLevels;
    }
}
