<?php

namespace IrisSweetsApi\Api\Sweets;

use IrisSweetsApi\Api\AbstractApi;
use IrisSweetsApi\Http\HttpClient;
use IrisSweetsApi\Exception\ApiException;

class GiveSweets extends AbstractApi
{
    public function __construct(HttpClient $httpClient, string $botId = '', string $irisToken = '')
    {
        parent::__construct($httpClient, $botId, $irisToken);
    }

    /**
     * Выдать ириски пользователю
     * 
     * @param int|float $sweets Количество ирисок для отправки
     * @param int $userId ID пользователя
     * @param string $comment Комментарий к отправке
     * @return array Ответ от API {"response":true} при успехе, {"response":false} при неудаче
 * @throws ApiException При ошибке запроса или неверных параметрах
     */
    public function give(int|float $sweets, int $userId, string $comment = ''): array
    {
        if ($sweets <= 0) {
            throw new ApiException('Количество ирисок должно быть больше 0');
        }

        if ($userId <= 0) {
            throw new ApiException('ID пользователя должен быть больше 0');
        }

        $params = [
            'sweets' => $sweets,
            'user_id' => $userId
        ];

        if (!empty($comment)) {
            $params['comment'] = $comment;
        }

        $response = $this->makeRequest('pocket/sweets/give', $params);

        if (!isset($response['result']) || $response['result'] !== true) {
            throw new ApiException('Неожиданный ответ от API: ' . json_encode($response));
        }

        return $response;
    }
}