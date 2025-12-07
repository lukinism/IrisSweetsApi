<?php

namespace IrisSweetsApi\Api\TgStars;

use IrisSweetsApi\Api\AbstractApi;
use IrisSweetsApi\Http\HttpClient;
use IrisSweetsApi\Exception\ApiException;

class GiveTgStars extends AbstractApi
{
    public function __construct(HttpClient $httpClient, string $botId = '', string $irisToken = '')
    {
        parent::__construct($httpClient, $botId, $irisToken);
    }

    /**
     * Передать тг-звёзды другому пользователю
     * 
     * @param int $tgstars Количество тг-звёзд для передачи
     * @param int $userId ID пользователя, которому передаются тг-звёзды
     * @param string $comment Подпись к переводу (необязательный параметр)
     * @return array Ответ от API {"result": int} - ID транзакции
     * @throws ApiException При ошибке запроса или неверных параметрах
     */
    public function give(int $tgstars, int $userId, string $comment = ''): array
    {
        if ($tgstars <= 0) {
            throw new ApiException('Количество тг-звёзд должно быть больше 0');
        }

        if ($userId <= 0) {
            throw new ApiException('ID пользователя должен быть больше 0');
        }

        $params = [
            'tgstars' => $tgstars,
            'user_id' => $userId
        ];

        if (!empty($comment)) {
            $params['comment'] = $comment;
        }

        $response = $this->makeRequest('pocket/tgstars/give', $params);

        if (!isset($response['result']) || !is_int($response['result'])) {
            throw new ApiException('Неожиданный ответ от API: ' . json_encode($response));
        }

        return $response;
    }
}

