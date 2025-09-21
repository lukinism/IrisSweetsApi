<?php

namespace IrisSweetsApi\Api;

use IrisSweetsApi\Http\HttpClient;
use IrisSweetsApi\Exception\ApiException;

class UserInfo extends AbstractApi
{
    public function __construct(HttpClient $httpClient, string $botId = '', string $irisToken = '')
    {
        parent::__construct($httpClient, $botId, $irisToken);
    }

    /**
     * Получить информацию о первом появлении пользователя в Ирисе
     * 
     * @param int $userId ID пользователя
     * @return array Ответ от API {"result": int}
     * @throws ApiException При ошибке запроса или неверных параметрах
     */
    public function getRegistration(int $userId): array
    {
        if ($userId <= 0) {
            throw new ApiException('ID пользователя должен быть больше 0');
        }

        $params = ['user_id' => $userId];
        $response = $this->makeRequest('user_info/reg', $params);

        if (!isset($response['result']) || !is_int($response['result'])) {
            throw new ApiException('Неожиданный ответ от API: ' . json_encode($response));
        }

        return $response;
    }

    /**
     * Получить статистику активности пользователя
     * 
     * @param int $userId ID пользователя
     * @return array Ответ от API {"result": {"day": int, "week": int, "month": int, "total": int}}
     * @throws ApiException При ошибке запроса или неверных параметрах
     */
    public function getActivity(int $userId): array
    {
        if ($userId <= 0) {
            throw new ApiException('ID пользователя должен быть больше 0');
        }

        $params = ['user_id' => $userId];
        $response = $this->makeRequest('user_info/activity', $params);

        if (!isset($response['result']) || !is_array($response['result'])) {
            throw new ApiException('Неожиданный ответ от API: ' . json_encode($response));
        }

        // Проверяем структуру ответа
        $result = $response['result'];
        if (!isset($result['day']) || !isset($result['week']) || 
            !isset($result['month']) || !isset($result['total'])) {
            throw new ApiException('Неверная структура ответа активности: ' . json_encode($response));
        }

        return $response;
    }

    /**
     * Получить информацию о нахождении пользователя в спам/скам/игнор базах
     * 
     * @param int $userId ID пользователя
     * @return array Ответ от API {"result": {"spam": bool, "scam": bool, "ignore": bool}}
     * @throws ApiException При ошибке запроса или неверных параметрах
     */
    public function getSpamInfo(int $userId): array
    {
        if ($userId <= 0) {
            throw new ApiException('ID пользователя должен быть больше 0');
        }

        $params = ['user_id' => $userId];
        $response = $this->makeRequest('user_info/spam', $params);

        if (!isset($response['result']) || !is_array($response['result'])) {
            throw new ApiException('Неожиданный ответ от API: ' . json_encode($response));
        }

        // Проверяем структуру ответа и нормализуем поля
        $result = $response['result'];
        if (!isset($result['is_spam']) || !isset($result['is_scam']) || !isset($result['is_ignore'])) {
            throw new ApiException('Неверная структура ответа спам-информации: ' . json_encode($response));
        }

        // Нормализуем структуру ответа для совместимости
        $normalizedResponse = [
            'result' => [
                'spam' => $result['is_spam'],
                'scam' => $result['is_scam'],
                'ignore' => $result['is_ignore']
            ]
        ];

        return $normalizedResponse;
    }

    /**
     * Получить звёздность пользователя
     * 
     * @param int $userId ID пользователя
     * @return array Ответ от API {"result": int}
     * @throws ApiException При ошибке запроса или неверных параметрах
     */
    public function getStars(int $userId): array
    {
        if ($userId <= 0) {
            throw new ApiException('ID пользователя должен быть больше 0');
        }

        $params = ['user_id' => $userId];
        $response = $this->makeRequest('user_info/stars', $params);

        if (!isset($response['result']) || !is_int($response['result'])) {
            throw new ApiException('Неожиданный ответ от API: ' . json_encode($response));
        }

        return $response;
    }

    /**
     * Получить информацию о мешке пользователя
     * 
     * @param int $userId ID пользователя
     * @return array Ответ от API {"result": {"sweets": double, "gold": int, "stars": int, "coins": int}}
     * @throws ApiException При ошибке запроса или неверных параметрах
     */
    public function getPocket(int $userId): array
    {
        if ($userId <= 0) {
            throw new ApiException('ID пользователя должен быть больше 0');
        }

        $params = ['user_id' => $userId];
        $response = $this->makeRequest('user_info/pocket', $params);

        if (!isset($response['result']) || !is_array($response['result'])) {
            throw new ApiException('Неожиданный ответ от API: ' . json_encode($response));
        }

        // Проверяем структуру ответа
        $result = $response['result'];
        if (!isset($result['sweets']) || !isset($result['gold']) || 
            !isset($result['stars']) || !isset($result['coins'])) {
            throw new ApiException('Неверная структура ответа мешка: ' . json_encode($response));
        }

        return $response;
    }

    /**
     * Получить несколько типов информации о пользователе одновременно
     * 
     * @param int $userId ID пользователя
     * @param array $permissions Массив разрешений ['reg', 'activity', 'spam', 'stars', 'pocket']
     * @return array Ответ от API с информацией по всем запрошенным разрешениям
     * @throws ApiException При ошибке запроса или неверных параметрах
     */
    public function getMultipleInfo(int $userId, array $permissions): array
    {
        if ($userId <= 0) {
            throw new ApiException('ID пользователя должен быть больше 0');
        }

        if (empty($permissions)) {
            throw new ApiException('Необходимо указать хотя бы одно разрешение');
        }

        // Валидируем разрешения
        $validPermissions = ['reg', 'activity', 'spam', 'stars', 'pocket'];
        foreach ($permissions as $permission) {
            if (!in_array($permission, $validPermissions)) {
                throw new ApiException("Неверное разрешение: $permission. Доступные: " . implode(', ', $validPermissions));
            }
        }

        // Делаем отдельные запросы для каждого разрешения
        $result = [];
        foreach ($permissions as $permission) {
            try {
                $response = match($permission) {
                    'reg' => $this->getRegistration($userId),
                    'activity' => $this->getActivity($userId),
                    'spam' => $this->getSpamInfo($userId),
                    'stars' => $this->getStars($userId),
                    'pocket' => $this->getPocket($userId),
                    default => throw new ApiException("Неизвестное разрешение: $permission")
                };
                $result[$permission] = $response['result'];
            } catch (ApiException $e) {
                // Если один из запросов не удался, продолжаем с остальными
                $result[$permission] = ['error' => $e->getMessage()];
            }
        }

        return ['result' => $result];
    }

    /**
     * Получить всю доступную информацию о пользователе
     * 
     * @param int $userId ID пользователя
     * @return array Ответ от API со всей информацией о пользователе
     * @throws ApiException При ошибке запроса или неверных параметрах
     */
    public function getAllInfo(int $userId): array
    {
        return $this->getMultipleInfo($userId, ['reg', 'activity', 'spam', 'stars', 'pocket']);
    }

    /**
     * Проверить, находится ли пользователь в спам-базе
     * 
     * @param int $userId ID пользователя
     * @return bool true если пользователь в спам-базе, false иначе
     * @throws ApiException При ошибке запроса
     */
    public function isSpam(int $userId): bool
    {
        $spamInfo = $this->getSpamInfo($userId);
        return $spamInfo['result']['spam'] === true;
    }

    /**
     * Проверить, находится ли пользователь в скам-базе
     * 
     * @param int $userId ID пользователя
     * @return bool true если пользователь в скам-базе, false иначе
     * @throws ApiException При ошибке запроса
     */
    public function isScam(int $userId): bool
    {
        $spamInfo = $this->getSpamInfo($userId);
        return $spamInfo['result']['scam'] === true;
    }

    /**
     * Проверить, находится ли пользователь в игнор-базе
     * 
     * @param int $userId ID пользователя
     * @return bool true если пользователь в игнор-базе, false иначе
     * @throws ApiException При ошибке запроса
     */
    public function isIgnored(int $userId): bool
    {
        $spamInfo = $this->getSpamInfo($userId);
        return $spamInfo['result']['ignore'] === true;
    }

    /**
     * Проверить, находится ли пользователь в любой из баз (спам/скам/игнор)
     * 
     * @param int $userId ID пользователя
     * @return bool true если пользователь в любой из баз, false иначе
     * @throws ApiException При ошибке запроса
     */
    public function isBlacklisted(int $userId): bool
    {
        $spamInfo = $this->getSpamInfo($userId);
        $result = $spamInfo['result'];
        return $result['spam'] === true || $result['scam'] === true || $result['ignore'] === true;
    }

    /**
     * Получить только активность пользователя за день
     * 
     * @param int $userId ID пользователя
     * @return int Активность за день
     * @throws ApiException При ошибке запроса
     */
    public function getDailyActivity(int $userId): int
    {
        $activity = $this->getActivity($userId);
        return $activity['result']['day'];
    }

    /**
     * Получить только активность пользователя за неделю
     * 
     * @param int $userId ID пользователя
     * @return int Активность за неделю
     * @throws ApiException При ошибке запроса
     */
    public function getWeeklyActivity(int $userId): int
    {
        $activity = $this->getActivity($userId);
        return $activity['result']['week'];
    }

    /**
     * Получить только активность пользователя за месяц
     * 
     * @param int $userId ID пользователя
     * @return int Активность за месяц
     * @throws ApiException При ошибке запроса
     */
    public function getMonthlyActivity(int $userId): int
    {
        $activity = $this->getActivity($userId);
        return $activity['result']['month'];
    }

    /**
     * Получить общую активность пользователя
     * 
     * @param int $userId ID пользователя
     * @return int Общая активность
     * @throws ApiException При ошибке запроса
     */
    public function getTotalActivity(int $userId): int
    {
        $activity = $this->getActivity($userId);
        return $activity['result']['total'];
    }

    /**
     * Получить только ириски из мешка пользователя
     * 
     * @param int $userId ID пользователя
     * @return float Количество ирисок
     * @throws ApiException При ошибке запроса
     */
    public function getPocketSweets(int $userId): float
    {
        $pocket = $this->getPocket($userId);
        return $pocket['result']['sweets'];
    }

    /**
     * Получить только голду из мешка пользователя
     * 
     * @param int $userId ID пользователя
     * @return int Количество голды
     * @throws ApiException При ошибке запроса
     */
    public function getPocketGold(int $userId): int
    {
        $pocket = $this->getPocket($userId);
        return $pocket['result']['gold'];
    }

    /**
     * Получить только звёзды из мешка пользователя
     * 
     * @param int $userId ID пользователя
     * @return int Количество звёзд
     * @throws ApiException При ошибке запроса
     */
    public function getPocketStars(int $userId): int
    {
        $pocket = $this->getPocket($userId);
        return $pocket['result']['stars'];
    }

    /**
     * Получить только монеты из мешка пользователя
     * 
     * @param int $userId ID пользователя
     * @return int Количество монет
     * @throws ApiException При ошибке запроса
     */
    public function getPocketCoins(int $userId): int
    {
        $pocket = $this->getPocket($userId);
        return $pocket['result']['coins'];
    }
}
