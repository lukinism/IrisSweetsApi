<?php

namespace IrisSweetsApi\Api\Gold;

use IrisSweetsApi\Api\AbstractApi;
use IrisSweetsApi\Http\HttpClient;
use IrisSweetsApi\Exception\ApiException;

class GetGoldHistory extends AbstractApi
{
    public function __construct(HttpClient $httpClient, string $botId = '', string $irisToken = '')
    {
        parent::__construct($httpClient, $botId, $irisToken);
    }

    /**
     * Получить историю операций с голдой
     * 
     * @param int $offset ID записи для смещения (по умолчанию 0 - с начала)
     * @return array Ответ от API с историей операций с голдой
     * @throws ApiException При ошибке запроса или неверных параметрах
     */
    public function getHistory(int $offset = 0): array
    {
        if ($offset < 0) {
            throw new ApiException('ID записи для смещения должен быть больше или равен 0');
        }

        $params = [];
        
        if ($offset > 0) {
            $params['offset'] = $offset;
        }

        $response = $this->makeRequest('pocket/gold/history', $params);
        
        return $response;
    }
}
