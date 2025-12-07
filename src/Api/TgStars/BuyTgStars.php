<?php

namespace IrisSweetsApi\Api\TgStars;

use IrisSweetsApi\Api\AbstractApi;
use IrisSweetsApi\Http\HttpClient;
use IrisSweetsApi\Exception\ApiException;

class BuyTgStars extends AbstractApi
{
    public function __construct(HttpClient $httpClient, string $botId = '', string $irisToken = '')
    {
        parent::__construct($httpClient, $botId, $irisToken);
    }

    /**
     * Покупка тг-звёзд за ириски
     * 
     * @param int $tgstars Количество тг-звёзд для покупки
     * @return array Ответ от API {"result": int} - ID транзакции
     * @throws ApiException При ошибке запроса или неверных параметрах
     */
    public function buy(int $tgstars): array
    {
        if ($tgstars <= 0) {
            throw new ApiException('Количество тг-звёзд должно быть больше 0');
        }

        $params = [
            'tgstars' => $tgstars
        ];

        $response = $this->makeRequest('pocket/tgstars/buy', $params);

        if (!isset($response['result']) || !is_int($response['result'])) {
            throw new ApiException('Неожиданный ответ от API: ' . json_encode($response));
        }

        return $response;
    }
}

