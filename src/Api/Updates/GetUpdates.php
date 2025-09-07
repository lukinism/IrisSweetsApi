<?php

namespace IrisSweetsApi\Api\Updates;

use IrisSweetsApi\Api\AbstractApi;
use IrisSweetsApi\Http\HttpClient;
use IrisSweetsApi\Exception\ApiException;

class GetUpdates extends AbstractApi
{
    public function __construct(HttpClient $httpClient, string $botId = '', string $irisToken = '', string $baseUrl = 'https://iris-tg.ru/api/v0.2/')
    {
        parent::__construct($httpClient, $botId, $irisToken, $baseUrl);
    }

    /**
     * Получить обновления событий
     * 
     * @param int $offset ID события для смещения (по умолчанию 0)
     * @return array Ответ от API с событиями
     * @throws ApiException При ошибке запроса или неверных параметрах
     */
    public function getUpdates(int $offset = 0): array
    {
        if ($offset < 0) {
            throw new ApiException('Offset должен быть больше или равен 0');
        }

        $params = [];
        
        if ($offset > 0) {
            $params['offset'] = $offset;
        }

        $response = $this->makeRequest('getUpdates', $params);
        
        return $response;
    }
}
