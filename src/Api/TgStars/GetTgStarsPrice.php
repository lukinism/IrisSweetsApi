<?php

namespace IrisSweetsApi\Api\TgStars;

use IrisSweetsApi\Api\AbstractApi;
use IrisSweetsApi\Http\HttpClient;
use IrisSweetsApi\Exception\ApiException;

class GetTgStarsPrice extends AbstractApi
{
    public function __construct(HttpClient $httpClient, string $botId = '', string $irisToken = '')
    {
        parent::__construct($httpClient, $botId, $irisToken);
    }

    /**
     * Оценка стоимости покупки тг-звёзд
     * Показывает, сколько ирисок требуется для покупки указанного количества тг-звёзд
     * 
     * @param int $tgstars Количество тг-звёзд для покупки
     * @return array Ответ от API {"result": {"tgstars": int, "sweets": int}}
     * @throws ApiException При ошибке запроса или неверных параметрах
     */
    public function getPrice(int $tgstars): array
    {
        if ($tgstars <= 0) {
            throw new ApiException('Количество тг-звёзд должно быть больше 0');
        }

        $params = [
            'tgstars' => $tgstars
        ];

        $response = $this->makeRequest('pocket/tgstars/price', $params);

        if (!isset($response['result']) || !is_array($response['result'])) {
            throw new ApiException('Неожиданный ответ от API: ' . json_encode($response));
        }

        return $response;
    }
}

