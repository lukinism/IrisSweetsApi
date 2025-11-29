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
     * @param int $donateScore Максимальное количество очков доната для использования (по умолчанию -1 - использовать максимально возможное)
     * @return array Ответ от API {"result": int} при успехе
 * @throws ApiException При ошибке запроса или неверных параметрах
     */
    public function give(int|float $sweets, int $userId, string $comment = '', int $donateScore = -1): array
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

        $params['donate_score'] = $donateScore;

        $response = $this->makeRequest('pocket/sweets/give', $params);

        if (!isset($response['result']) || !is_int($response['result'])) {
            throw new ApiException('Неожиданный ответ от API: ' . json_encode($response));
        }

        return $response;
    }
}