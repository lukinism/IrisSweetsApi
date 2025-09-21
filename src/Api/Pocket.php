<?php

namespace IrisSweetsApi\Api;

use IrisSweetsApi\Http\HttpClient;
use IrisSweetsApi\Exception\ApiException;

class Pocket extends AbstractApi
{
    public function __construct(HttpClient $httpClient, string $botId = '', string $irisToken = '')
    {
        parent::__construct($httpClient, $botId, $irisToken);
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

    /**
     * Передать очки доната пользователю
     * 
     * @param int $amount Количество очков доната
     * @param int $userId ID пользователя, которому передаются очки доната
     * @param string $comment Подпись к переводу (необязательный параметр)
     * @return array Ответ от API {"result": int} - ID транзакции
     * @throws ApiException При ошибке запроса или неверных параметрах
     */
    public function giveDonateScore(int $amount, int $userId, string $comment = ''): array
    {
        if ($amount <= 0) {
            throw new ApiException('Количество очков доната должно быть больше 0');
        }

        if ($userId <= 0) {
            throw new ApiException('ID пользователя должен быть больше 0');
        }

        $params = [
            'amount' => $amount,
            'user_id' => $userId
        ];

        if (!empty($comment)) {
            $params['comment'] = $comment;
        }

        $response = $this->makeRequest('pocket/donate_score/give', $params);

        if (!isset($response['result']) || !is_int($response['result'])) {
            throw new ApiException('Неожиданный ответ от API: ' . json_encode($response));
        }

        return $response;
    }

    /**
     * Получить историю изменения очков доната в мешке бота
     * 
     * @param int $offset Будут выданы записи с id >= offset (необязательный параметр)
     * @param int $limit Количество записей в ответе (необязательный параметр, по умолчанию 200)
     * @return array Массив записей истории очков доната
     * @throws ApiException При ошибке запроса
     */
    public function getDonateScoreHistory(int $offset = 0, int $limit = 200): array
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

        $response = $this->makeRequest('pocket/donate_score/history', $params);

        if (!isset($response) || !is_array($response)) {
            throw new ApiException('Неожиданный ответ от API: ' . json_encode($response));
        }

        return $response;
    }
}
