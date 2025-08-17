<?php

namespace IrisSweetsApi\Api;

use IrisSweetsApi\Http\HttpClient;
use IrisSweetsApi\Exception\ApiException;

class Pocket extends AbstractApi
{
    public function __construct(HttpClient $httpClient, string $botId = '', string $irisToken = '', string $baseUrl = 'https://iris-tg.ru/api/')
    {
        parent::__construct($httpClient, $botId, $irisToken, $baseUrl);
    }

    /**
     * Открыть доступ к мешку
     * 
     * @return array Ответ от API {"response":true} при успехе, {"response":false} при неудаче
     * @throws ApiException При ошибке запроса
     */
    public function enable(): array
    {
        $response = $this->makeRequest('pocket/enable');

        if (!isset($response['response']) || $response['response'] !== true) {
            throw new ApiException('Операция не выполнена: ' . json_encode($response));
        }

        return $response;
    }

    /**
     * Закрыть доступ к мешку
     * 
     * @return array Ответ от API {"response":true} при успехе, {"response":false} при неудаче
     * @throws ApiException При ошибке запроса
     */
    public function disable(): array
    {
        $response = $this->makeRequest('pocket/disable');

        if (!isset($response['response']) || $response['response'] !== true) {
            throw new ApiException('Неожиданный ответ от API: ' . json_encode($response));
        }

        return $response;
    }

    /**
     * Разрешить всем переводить в мешок
     * 
     * @return array Ответ от API {"response":true} при успехе, {"response":false} при неудаче
     * @throws ApiException При ошибке запроса
     */
    public function allow_all(): array
    {
        $response = $this->makeRequest('pocket/allow_all');

        if (!isset($response['response']) || $response['response'] !== true) {
            throw new ApiException('Операция не выполнена: ' . json_encode($response));
        }

        return $response;
    }

    /**
     * Запретить всем переводить в мешок
     * 
     * @return array Ответ от API {"response":true} при успехе, {"response":false} при неудаче
     * @throws ApiException При ошибке запроса
     */
    public function deny_all(): array
    {
        $response = $this->makeRequest('pocket/deny_all');

        if (!isset($response['response']) || $response['response'] !== true) {
            throw new ApiException('Операция не выполнена: ' . json_encode($response));
        }

        return $response;
    }

    /**
     * Разрешить конкретному пользователю переводить в мешок
     * 
     * @param int $userId ID пользователя для разрешения
     * @return array Ответ от API {"response":true} при успехе, {"response":false} при неудаче
     * @throws ApiException При ошибке запроса или неверных параметрах
     */
    public function allow_user(int $userId): array
    {
        if ($userId <= 0) {
            throw new ApiException('ID пользователя должен быть больше 0');
        }

        $params = [
            'user_id' => $userId
        ];

        $response = $this->makeRequest('pocket/allow_user', $params);

        if (!isset($response['response']) || $response['response'] !== true) {
            throw new ApiException('Операция не выполнена: ' . json_encode($response));
        }

        return $response;
    }

    /**
     * Запретить конкретному пользователю переводить в мешок
     * 
     * @param int $userId ID пользователя для запрета
     * @return array Ответ от API {"response":true} при успехе, {"response":false} при неудаче
     * @throws ApiException При ошибке запроса или неверных параметрах
     */
    public function deny_user(int $userId): array
    {
        if ($userId <= 0) {
            throw new ApiException('ID пользователя должен быть больше 0');
        }

        $params = [
            'user_id' => $userId
        ];

        $response = $this->makeRequest('pocket/deny_user', $params);

        if (!isset($response['response']) || $response['response'] !== true) {
            throw new ApiException('Операция не выполнена: ' . json_encode($response));
        }

        return $response;
    }
}
