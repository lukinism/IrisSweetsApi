<?php

namespace IrisSweetsApi\Api\Updates;

use IrisSweetsApi\Http\HttpClient;

class Updates
{
    private HttpClient $httpClient;
    private string $botId;
    private string $irisToken;

    public function __construct(HttpClient $httpClient, string $botId = '', string $irisToken = '', string $baseUrl = 'https://iris-tg.ru/api/v0.2/')
    {
        $this->httpClient = $httpClient;
        $this->botId = $botId;
        $this->irisToken = $irisToken;
    }

    /**
     * Получить обновления событий
     * 
     * @param int $offset ID события для смещения (по умолчанию 0)
     * @return array Ответ от API с событиями
     * @throws \IrisSweetsApi\Exception\ApiException При ошибке запроса или неверных параметрах
     */
    public function getUpdates(int $offset = 0): array
    {
        $getUpdates = new GetUpdates($this->httpClient, $this->botId, $this->irisToken);
        return $getUpdates->getUpdates($offset);
    }
}




