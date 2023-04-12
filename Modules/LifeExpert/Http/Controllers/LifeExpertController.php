<?php

namespace Modules\LifeExpert\Http\Controllers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Routing\Controller;
use Psr\Http\Message\ResponseInterface;

class LifeExpertController extends Controller
{
    /**
     * Рассчитать астрологические данные пользователя и вернуть их.
     * @param string $name Имя пользователя.
     * @param int $day День рождения пользователя.
     * @param int $month Месяц рождения пользователя.
     * @param int $year Год рождения пользователя.
     * @param int $hour Час рождения пользователя.
     * @param int $minute Минута рождения пользователя.
     * @return array Астрологические данные пользователя.
     * @throws GuzzleException Если запрос к сервису Lifexpert не удался.
     */
    public function calculate(string $name, int $day, int $month, int $year, int $hour, int $minute): array
    {
        $url = "https://www.lifexpert.ru/jui/rpc/";

        $client = new Client();

        $cookies = $this->getCookies($client,$url);

        $headers = $this->getHeaders($cookies, $name, $day, $month, $year, $hour, $minute);

        $data = $this->getData($year, $month, $day, $hour, $minute, $name);

        $response = $this->sendRequest($client, $url, $headers, $data);

        return $this->parseResponse($response);
    }

    /**
     * Получить куки из заголовков ответа.
     *
     * @param Client $client Объект Guzzle HTTP клиента.
     * @return array Куки из заголовков ответа.
     * @throws GuzzleException Если запрос к сервису Lifexpert не удался.
     */
    private function getCookies(Client $client): array
    {
        $response = $client->request('GET', 'https://www.lifexpert.ru/');
        return $response->getHeader('Set-Cookie');
    }

    /**
     * Получить заголовки запроса.
     * @param array $cookies Куки из заголовков ответа.
     * @param string $name Имя пользователя.
     * @param string $day День рождения пользователя.
     * @param string $month Месяц рождения пользователя.
     * @param string $year Год рождения пользователя.
     * @param string $hour Час рождения пользователя.
     * @param string $minute Минута рождения пользователя.
     * @return array Заголовки запроса.
     * @return array
     */
    private function getHeaders(array $cookies, string $name, string $day, string $month, string $year, string $hour, string $minute): array
    {
        return [
            'Host' => 'www.lifexpert.ru',
            'Cookie' => "{$cookies[0]}; astro.control=%7B%22current%22%3A%22{$year}-{$month}-{$day}%20{$hour}%3A{$minute}%3A00%22%2C%22current_coords%22%3A%22%7B%5C%22lat%5C%22%3A59.9503%2C%5C%22lng%5C%22%3A30.3903%2C%5C%22country%5C%22%3A%5C%22RU%5C%22%2C%5C%22verbose%5C%22%3A%5C%22%D0%A6%D0%B5%D0%BD%D1%82%D1%80%D0%B0%D0%BB%D1%8C%D0%BD%D1%8B%D0%B9%20%D1%80-%D0%BD%2C%20%D0%A1%D0%B0%D0%BD%D0%BA%D1%82-%D0%9F%D0%B5%D1%82%D0%B5%D1%80%D0%B1%D1%83%D1%80%D0%B3%5C%22%7D%22%2C%22houses%22%3A%22K%22%7D",
            'Content-Length' => '514',
            'Sec-Ch-Ua' => '"Not A(Brand";v="24", "Chromium";v="110"',
            'Accept' => 'application/json, text/javascript, */*; q=0.01',
            'Content-Type' => 'application/json',
            'X-Requested-With' => 'XMLHttpRequest',
            'Sec-Ch-Ua-Mobile' => '?0',
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/110.0.5481.78 Safari/537.36',
            'Sec-Ch-Ua-Platform' => '"Windows"',
            'Origin' => 'https://www.lifexpert.ru',
            'Sec-Fetch-Site' => 'same-origin',
            'Sec-Fetch-Mode' => 'cors',
            'Sec-Fetch-Dest' => 'empty',
            'Referer' => "https://www.lifexpert.ru/tools/astropifagor/?current={$year}-{$month}-{$day}:{$hour}:{$minute}:00&current_coords={'test'}&houses=K",
            'Accept-Encoding' => 'gzip, deflate',
            'Accept-Language' => 'ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7',
        ];
    }

    /**
     * Получить данные запроса.
     *
     * @param string $name Имя пользователя.
     * @param string $day День рождения пользователя.
     * @param string $month Месяц рождения пользователя.
     * @param string $year Год рождения пользователя.
     * @param string $hour Час рождения пользователя.
     * @param string $minute Минута рождения пользователя.
     * @return string Данные запроса.
     */
    private function getData(string $year, string $month, string $day, string $hour, string $minute, string $name): string
    {
        return '[
            {"jsonrpc":"2.0","id":1,"method":"billing.get_cart","params":{}},
            {"jsonrpc":"2.0","id":2,"method":"datetime_calcs.astro_frame","params":{"date":"'.$year.'-'.$month.'-'.$day.' '.$hour.':'.$minute.':00","coord":{},"options":{"houses_system":"K","use_aspect_node":false,"use_aspect_lilith":false}}},
            {"jsonrpc":"2.0","id":3,"method":"datetime_calcs.destinywill","params":{"date":"'.$year.'-'.$month.'-'.$day.' '.$hour.':'.$minute.':00"}},
            {"jsonrpc":"2.0","id":4,"method":"datetime_calcs.pifagor_frame","params":{"date":"'.$year.'-'.$month.'-'.$day.' '.$hour.':'.$minute.':00","death_date":null,"fio":["", "'.$name.'",""],"use_1999":false}}]';
    }

    /**
     * Отправить запрос и вернуть ответ.
     *
     * @param Client $client
     * @param string $url
     * @param array $headers
     * @param string $data
     * @return ResponseInterface
     * @throws GuzzleException
     */
    private function sendRequest(Client $client, string $url, array $headers, string $data): ResponseInterface
    {
        return $client->post($url, [
            'headers' => $headers,
            'body' => $data,
            'verify' => false // for debug only!
        ]);
    }

    /**
     * Разберите ответ и верните астрологические данные в виде ассоциативного массива.
     *
     * @param ResponseInterface $response
     * @return array
     */
    private function parseResponse(ResponseInterface $response): array
    {
        $body = $response->getBody();

        $obj = json_decode($body);

        $elements = $obj[1]->result->elements->elements;
        $strategies = $obj[1]->result->elements->strategy;
        $planets = $obj[1]->result->planets;

        return [
            'elements' => $elements,
            'strategies' => $strategies,
            'planets' => $planets
        ];
    }
}
