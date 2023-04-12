<?php

namespace Modules\Astro\Http\Controllers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Routing\Controller;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\DomCrawler\Crawler;

class AstroController extends Controller
{

    /**
     * Метод для расчета гороскопа на основе переданных параметров.
     * @param string $name Имя человека
     * @param int $day День рождения
     * @param int $month Месяц рождения
     * @param int $year Год рождения
     * @param int $hour Час рождения
     * @param int $minute Минута рождения
     * @param mixed $city Информация о городе (cityId, latitude, longitude)
     * @param mixed $GMT Часовой пояс в формате GMT
     * @return array Массив с данными о первичном и вторичном знаке зодиака
     * @throws GuzzleException
     */
    public function calculate(string $name, int $day, int $month, int $year, int $hour, int $minute, mixed $city, mixed $GMT): array
    {
    // Создание экземпляра клиента для отправки запроса
        $client = new Client();
    // URL-адрес сайта для отправки запроса
        $url = 'https://astro-online.ru/view.php';

    // Получение cookies из ответа
        $cookies = $this->getCookies($client, $url);
    // Получение значения ssid из cookies
        $ssid = ["ssid" => explode("=", explode(";", $cookies[0])[0])[1]];

    // Отправка POST-запроса и обработка ответа
        $response = $this->sendRequest($client, $url, $this->prepareData($name, $day, $month, $year, $hour, $minute, $ssid, $city['cityId'], $city['latitude'], $city['longitude'], $GMT), $this->sendHeader($cookies));
        $rawText = $this->parseResponse($response);

    // Возврат массива с данными о первичном и вторичном знаке зодиака
        return $this->processResponse($rawText);
    }

    /**
     * Получить куки из заголовков ответа.
     *
     * @param Client $client
     * @param $url
     * @return array
     * @throws GuzzleException
     */
    private function getCookies(Client $client, $url): array
    {
        // Отправляем GET запрос на главную страницу сайта, чтобы получить cookie-файлы
        $response = $client->request('GET', $url);

        return $response->getHeader('Set-Cookie');
    }

    /**
     * Подготавливает данные для отправки запроса.
     *
     * @param string $name Имя человека.
     * @param int $day День рождения.
     * @param int $month Месяц рождения.
     * @param int $year Год рождения.
     * @param int $hour Час рождения.
     * @param int $minute Минута рождения.
     * @param array $cookies Массив cookie-файлов.
     * @return array|null Массив данных для отправки запроса.
     */
    private function prepareData(string $name, int $day, int $month, int $year, int $hour, int $minute, array $cookies, $cityId, $cityN, $cityE, $GMT): ?array
    {
            $dataArray = [
            'u_namez' =>$name,
            'dayz' => $day,
            'monthz' => $month,
            'yearz' => $year,
            'hourz' => $hour,
            'minutz' => $minute,
            'N' => $cityN,
            'E' => $cityE,
            'city' => $cityId,
            'frm_autogmt'=> 'on',
            'time_gmt' => $GMT
        ];
        // Остальная информация
        $dataLast = [
            "sun" => "yes",
            "moon" => "yes",
            "mercury" => "yes",
            "venus" => "yes",
            "mars" => "yes",
            "jupiter" => "yes",
            "saturn" => "yes",
            "uran" => "yes",
            "neptun" => "yes",
            "pluton" => "yes",
            "node" => "yes",
            "snode" => "no",
            "lilit" => "no",
            "selena" => "no",
            "prozerpina" => "no",
            "hiron" => "no",
            "het1" => "no",
            "sakoyan1" => "yes",
            "globa1" => "no",
            "podvodniy1" => "no",
            "katrin1" => "no",
            "mariya1" => "no",
            "het2" => "no",
            "podvodniy2" => "no",
            "katrin2" => "yes",
            "globa3" => "yes",
            "het4" => "no",
            "sakoyan4" => "no",
            "podvodniy3" => "no",
            "izraitel4" => "yes",
            "ruler5" => "yes",
            "tranz10" => "yes",
            "solar12" => "yes",
            "shulman14" => "yes",
            "nazarova9" => "yes",
            "nazarova10" => "yes",
            "nazarova11" => "yes",
            "sakoyan10" => "no",
            "conj1" => "0",
            "conj2" => "5",
            "sekst1" => "52",
            "sekst2" => "68",
            "kvad1" => "82",
            "kvad2" => "98",
            "trin1" => "112",
            "trin2" => "128",
            "opp1" => "172",
            "opp2" => "180",
            "leto_zima" => "yes",
            "sort_asp" => "0",
            "gamma" => "1",
            "house" => "0",
            "cat_id" => "1",
            ];
        // Соединяем массивы
        return  array_merge($dataArray,$dataLast,$cookies);
    }

    /**
     * Отправляет заголовки на сервер.
     *
     * @param array $cookies
     * @return array Ответ от сервера.
     */
    private function sendHeader(array $cookies): array
    {

        // Создаем заголовки
        return [
            'Accept'            => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
            'Accept-Encoding'   => 'gzip, deflate, br',
            'Accept-Language'   => 'ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7',
            'Cache-Control'     => 'no-cache',
            'Connection'        => 'keep-alive',
            'Content-Length'    => 764,
            'Content-Type'      => 'application/x-www-form-urlencoded',
            'Cookie'            => $cookies,
            'Host'              => 'astro-online.ru',
            'Origin'            => 'https://astro-online.ru',
            'Pragma'            => 'no-cache',
            'Referer'           => 'https://astro-online.ru/natal.html',
            'sec-ch-ua'         => '"Chromium";v="108", "Opera GX";v="94", "Not)A;Brand";v="99"',
            'sec-ch-ua-mobile'  => '?0',
            'sec-ch-ua-platform'=> "Windows",
            'Sec-Fetch-Dest'    => 'document',
            'Sec-Fetch-Mode'    => 'navigate',
            'Sec-Fetch-Site'    => 'same-origin',
            'Sec-Fetch-User'    => '?1',
            'Upgrade-Insecure-Requests'=> 1,
            'User-Agent'        => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.0.0 Safari/537.36 OPR/94.0.0.0'
        ];
    }

    /**
     * Отправляет запрос, и возвращает ответ.
     *
     * @param Client $client
     * @param string $url
     * @param array $prepareData
     * @param array $sendHeader
     * @return ResponseInterface
     * @throws GuzzleException
     */
    private function sendRequest(Client $client, string $url, array $prepareData, array $sendHeader): ResponseInterface
    {
        return $client->post($url, [
            'form_params' => $prepareData,
            'headers' => $sendHeader,
        ]);
    }

    /**
     * Разбирает ответ сервера и возвращает данные астрологической информации в виде строки.
     *
     * @param ResponseInterface $response
     * @return string
     */
    private function parseResponse(ResponseInterface $response): string
    {
        // Получение тела ответа в виде строки и создание экземпляра класса Crawler, используя библиотеку Symfony
        $html = $response->getBody()->getContents();
        $crawler = new Crawler($html);

        // Извлечение содержимого элемента div с идентификатором "little"
        return $crawler->filterXPath('//*[@id="little"]/div[1]')->innerText();
    }

    /**
     * Обрабатывает ответ от сервера, и возвращает результат.
     *
     * @param $client
     * @param $url
     * @param $prepareData
     * @param $sendHeader
     * @return array Результат обработки ответа.
     */
    private function processResponse($string): array
    {
        //Создание ассоциативного массива для преобразования знаков зодиака из строкового значения в числовое
        $zodiacSigns = [
            "Овен" => 1,
            "Телец" => 2,
            "Близнецы" => 3,
            "Рак" => 4,
            "Лев" => 5,
            "Дева" => 6,
            "Весы" => 7,
            "Скорпион" => 8,
            "Стрелец" => 9,
            "Козерог" => 10,
            "Водолей" => 11,
            "Рыбы" => 12
        ];

        //Инициализация переменных для первичного и вторичного знаков зодиака
        $primary_zodiac_sign_number = null;
        $secondary_zodiac_sign_number = null;

        // Извлечение первичного знака зодиака из текста
        preg_match('/солнечный знак - ([\p{L}\s]+)/ui', $string, $matches);
        if (!empty($matches[1]) && isset($zodiacSigns[$matches[1]])) {
            $primary_zodiac_sign_number = $zodiacSigns[$matches[1]];
        }

        // Извлечение вторичного знака зодиака из текста
        preg_match('/от знака ([\p{L}\s]+)/ui', $string, $matches);
        if (!empty($matches[1]) && isset($zodiacSigns[$matches[1]])) {
            $secondary_zodiac_sign_number = $zodiacSigns[$matches[1]];
        }

        //Возврат результата в виде ассоциативного массива, содержащего числовые значения первичного и вторичного знаков зодиака
        return [
            'primary' => $primary_zodiac_sign_number,
            'secondary' => $secondary_zodiac_sign_number
        ];
    }
}
