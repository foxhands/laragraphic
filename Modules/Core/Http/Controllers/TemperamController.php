<?php

namespace Modules\Core\Http\Controllers;

use Exception;
use Illuminate\Routing\Controller;
use Modules\Core\Http\Services\TemperamService;

class TemperamController extends Controller
{


    /**
     * Вычисляет стихийное значение для двух людей на основе их космограмм.
     *
     * @param array $data Данные для вычисления стихийного значения.
     * @return array Стихийное значение в формате "XXYY", где XX - стихия мужчины, YY - стихия женщины.
     * @throws Exception
     */
    public function calculate(array $data): string
    {
        [$maleElement, $femaleElement] = array_map(fn ($person) => json_decode(json_encode($person['cosmo']), true)['elements'], [$data['male'], $data['female']]);
        [$maleStrategy, $femaleStrategy] = array_map(fn ($person) => json_decode(json_encode($person['cosmo']), true)['strategies'], [$data['male'], $data['female']]);

        $maleMaxElement = $this->getMaxElement($maleElement);
        $maleMaxStrategy = $this->getMaxStrategy($maleStrategy);

        $femaleMaxElement = $this->getMaxElement($femaleElement);
        $femaleMaxStrategy = $this->getMaxStrategy($femaleStrategy);

        $totalElementsPercentage = $this->getTotalPercentageElement($maleElement, $femaleElement);
        $totalStrategyPercentage = $this->getTotalPercentageStrategy($maleMaxStrategy, $femaleMaxStrategy);

        if ($maleMaxElement['name'] !== $femaleMaxElement['name']){
            $elementTotal = 'C0C0';
        }else{
            $elementTotal = $this->calculatePercentagesElement($maleMaxElement, $femaleMaxElement, $totalElementsPercentage);
        }
        if ($maleMaxStrategy['name'] !== $femaleMaxStrategy['name']){
            $strategyTotal = 'F0F0';
        }else{
            $strategyTotal = $this->calculatePercentagesStrategy($maleMaxStrategy, $femaleMaxStrategy, $totalStrategyPercentage);

        }

        return $elementTotal.$strategyTotal;
    }

    /**
     * Вычисляет общий процент совместимости по элементам между мужчиной и женщиной
     *
     * @param array $male Массив значений элементов мужчины
     * @param array $female Массив значений элементов женщины
     * @return string Общий процент совместимости по элементам
     */
    private function getTotalPercentageElement(array $male, array $female): string
    {
        // Получаем минимальный процент для каждого элемента
        $minimums = array_map(fn ($element) => min($male[$element], $female[$element]), ['fire', 'earth', 'air', 'water']);
        // Вычисляем общий процент для всех элементов
        $total = array_sum($minimums);
        // Возвращаем общий процент
        return number_format($total * 100, 1);
    }

    private function getTotalPercentageStrategy(array $male, array $female): string
    {
        // Получаем минимальный процент для каждого элемента
        $minimums = array_map(function ($strategies) use ($male, $female) {
            if (!isset($male[$strategies]) || !isset($female[$strategies])) {
                return 0;
            }
            return min($male[$strategies], $female[$strategies]);
        }, ['cardinal', 'constant', 'mutable']);
        // Вычисляем общий процент для всех элементов
        $total = array_sum($minimums);
        // Возвращаем общий процент
        return number_format($total * 100, 1);
    }

    /**
     * Возвращает массив с максимальным элементом по процентам.
     *
     * @param array $elements Массив элементов в формате ['name' => 'название', 'percent' => 'процент']
     * @return array Массив с максимальным элементом в формате ['name' => 'название', 'percent' => 'процент в формате с плавающей запятой и одним знаком после запятой']
     */
    private function getMaxElement(array $elements): array
    {
        arsort($elements);
        $maxElement = key($elements);
        $maxPercent = number_format(current($elements) * 100, 1);
        return ['name' => $maxElement, 'percent' => $maxPercent];
    }
    private function getMaxStrategy(array $strategies): array
    {
        arsort($strategies);
        $maxStrategy = key($strategies);
        $maxPercent = number_format(current($strategies) * 100, 1);
        return ['name' => $maxStrategy, 'percent' => $maxPercent];
    }

    /**
     * Функция для вычисления процентов наибольшей стихии и общих процентов для всех стихий
     *
     * @param array $maleMaxElement Наибольшая мужская стихия
     * @param array $femaleMaxElement Наибольшая женская стихия
     * @param string $totalElementsPercentage
     * @return string Массив с процентами наибольшей стихии и общими процентами для всех стихий
     * @throws Exception
     */
    private function calculatePercentagesElement(array $highestMaleElement, array $highestFemaleElement, float $totalElementPercentage): string
    {
        // Define the ranges for each indicator
        $indicatorRanges = [
            'C1' => [26, 42], 'C2' => [26, 42], 'C3' => [26, 42], 'C4' => [26, 42], 'C5' => [26, 42],
            'D2' => [42, 60], 'D3' => [42, 60], 'D4' => [42, 60], 'D5' => [42, 60],
            'E3' => [61, 100], 'E4' => [61, 100], 'E5' => [61, 100]
        ];

        $percentageRanges = [
            'C1' => [0, 72], 'C2' => [72, 79], 'C3' => [79, 86], 'C4' => [86, 93], 'C5' => [93, 100],
            'D2' => [0, 72], 'D3' => [72, 79], 'D4' => [79, 86], 'D5' => [86, 100],
            'E3' => [0, 72], 'E4' => [72, 79], 'E5' => [79, 100]
        ];

        $malePercentages = $this->getIndicatorPercentagesElement($indicatorRanges, $percentageRanges, $highestMaleElement['percent'], $totalElementPercentage);
        $femalePercentages = $this->getIndicatorPercentagesElement($indicatorRanges, $percentageRanges, $highestFemaleElement['percent'], $totalElementPercentage);

        return $malePercentages.$femalePercentages;
    }

    private function calculatePercentagesStrategy(array $highestMaleStrategy, array $highestFemaleMaleStrategy, float $totalStrategyPercentage): string
    {
        // Define the ranges for each indicator
        $indicatorRanges = [
            'F1' => [34, 49], 'F2' => [34, 49], 'F3' => [34, 49], 'F4' => [34, 49], 'F5' => [34, 49],
            'G2' => [55, 65], 'G3' => [55, 65], 'G4' => [55, 65], 'G5' => [55, 65],
            'H3' => [66, 100], 'H4' => [66, 100], 'H5' => [66, 100]
        ];

        $percentageRanges = [
            'F1' => [0, 72], 'F2' => [72, 79], 'F3' => [79, 86], 'F4' => [86, 93], 'F5' => [93, 100],
            'G2' => [0, 72], 'G3' => [72, 79], 'G4' => [79, 86], 'G5' => [86, 100],
            'H3' => [0, 72], 'H4' => [72, 79], 'H5' => [79, 100]
        ];

        $malePercentages = $this->getIndicatorPercentagesStrategy($indicatorRanges, $percentageRanges, $highestMaleStrategy['percent'], $totalStrategyPercentage);
        $femalePercentages = $this->getIndicatorPercentagesStrategy($indicatorRanges, $percentageRanges, $highestFemaleMaleStrategy['percent'], $totalStrategyPercentage);

        return $malePercentages.$femalePercentages;
    }

    /**
     * Возвращает значение показателя для индикатора, основанного на заданных диапазонах и максимальных значениях.
     *
     * @param array $rangesElement Диапазоны элементов, для которых необходимо получить индикатор.
     * @param array $rangesPercent Диапазоны процентного соотношения, для которых необходимо получить индикатор.
     * @param float $maxValue Максимальное значение элемента.
     * @param float $totalValue Общее значение элемента в процентном соотношении.
     * @return string Возвращает строку, представляющую индикатор для заданных диапазонов и максимальных значений.
     */
    private function getIndicatorPercentagesElement(array $rangesElement, array $rangesPercent, float $maxValue, float $totalValue): string
    {
        $elementIndicator = $this->getMatchingIndicatorElement($rangesElement, $maxValue);
        $percentIndicator = $this->getMatchingIndicatorElement($rangesPercent, $totalValue);

        $indicators = array_intersect($elementIndicator, $percentIndicator);

        if (count($indicators) > 0) {
            // Return the first matching indicator
            return reset($indicators);
        } else {
            // Return the default indicator 'C0'
            return 'C0';
        }
    }

    private function getIndicatorPercentagesStrategy(array $rangesStrategy, array $rangesPercent, float $maxValue, float $totalValue): string
    {
        $elementIndicator = $this->getMatchingIndicatorStrategy($rangesStrategy, $maxValue);
        $percentIndicator = $this->getMatchingIndicatorStrategy($rangesPercent, $totalValue);

        $indicators = array_intersect($elementIndicator, $percentIndicator);

        if (count($indicators) > 0) {
            // Return the first matching indicator
            return reset($indicators);
        } else {
            // Return the default indicator 'C0'
            return 'F0';
        }
    }

    /**
     * Возвращает массив индикаторов, соответствующих заданному значению, для заданных диапазонов.
     *
     * @param array $ranges Массив диапазонов, где ключом является индикатор, а значением - массив, содержащий
     * начальное и конечное значения диапазона.
     * @param float $value Значение, для которого требуется найти соответствующие индикаторы.
     * @return array Массив индикаторов, которые соответствуют заданному значению.
     */
    private function getMatchingIndicatorElement(array $ranges, float $value): array
    {
        return array_keys(array_filter($ranges, fn ($range) => $value >= $range[0] && $value <= $range[1]));
    }

    private function getMatchingIndicatorStrategy(array $ranges, float $value): array
    {
        return array_keys(array_filter($ranges, fn ($range) => $value >= $range[0] && $value <= $range[1]));
    }


}
