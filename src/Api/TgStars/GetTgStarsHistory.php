<?php

namespace IrisSweetsApi\Api\TgStars;

use IrisSweetsApi\Api\AbstractApi;
use IrisSweetsApi\Http\HttpClient;
use IrisSweetsApi\Exception\ApiException;

class GetTgStarsHistory extends AbstractApi
{
    public function __construct(HttpClient $httpClient, string $botId = '', string $irisToken = '')
    {
        parent::__construct($httpClient, $botId, $irisToken);
    }

    /**
     * Получить историю изменения тг-звёзд в мешке бота
     * 
     * @param int $offset Будут выданы записи с id >= offset (необязательный параметр, по умолчанию выдаются limit последних записей)
     * @param int $limit Количество записей в ответе (необязательный параметр, по умолчанию 200)
     * @return array Массив записей истории тг-звёзд
     * @throws ApiException При ошибке запроса или неверных параметрах
     */
    public function getHistory(int $offset = 0, int $limit = 200): array
    {
        if ($offset < 0) {
            throw new ApiException('Offset не может быть отрицательным');
        }

        if ($limit <= 0 || $limit > 1000) {
            throw new ApiException('Limit должен быть от 1 до 1000');
        }

        $params = [];
        
        if ($offset > 0) {
            $params['offset'] = $offset;
        }
        
        if ($limit !== 200) {
            $params['limit'] = $limit;
        }

        $response = $this->makeRequest('pocket/tgstars/history', $params);

        if (!isset($response) || !is_array($response)) {
            throw new ApiException('Неожиданный ответ от API: ' . json_encode($response));
        }

        return $response;
    }
}

