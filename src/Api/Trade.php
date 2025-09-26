<?php

namespace IrisSweetsApi\Api;

use IrisSweetsApi\Http\HttpClient;
use IrisSweetsApi\Exception\ApiException;

class Trade extends AbstractApi
{
    public function __construct(HttpClient $httpClient, string $botId = '', string $irisToken = '')
    {
        parent::__construct($httpClient, $botId, $irisToken);
    }

    /**
     * Заявка на покупку ирис-голд
     * 
     * @param float $price Цена покупки (от 0.01 до 1000000)
     * @param float $volume Желаемое количество голды для покупки
     * @return array Ответ от API с результатом покупки
     * @throws ApiException При ошибке запроса или неверных параметрах
     */
    public function buy(float $price, float $volume): array
    {
        if ($price < 0.01 || $price > 1000000) {
            throw new ApiException('Цена должна быть от 0.01 до 1000000');
        }

        if ($volume <= 0) {
            throw new ApiException('Объем должен быть больше 0');
        }

        $params = [
            'price' => $price,
            'volume' => $volume
        ];

        $response = $this->makeRequest('trade/buy', $params);

        // API может возвращать данные напрямую или в обертке result
        if (isset($response['result']) && is_array($response['result'])) {
            $result = $response['result'];
        } elseif (is_array($response) && isset($response['done_volume'])) {
            // Если данные пришли напрямую, оборачиваем в result
            $result = $response;
            $response = ['result' => $result];
        } else {
            throw new ApiException('Неожиданный ответ от API: ' . json_encode($response));
        }

        // Проверяем структуру ответа
        if (!isset($result['done_volume']) || !isset($result['sweets_spent'])) {
            throw new ApiException('Неверная структура ответа: ' . json_encode($response));
        }

        return $response;
    }

    /**
     * Заявка на продажу ирис-голд
     * 
     * @param float $price Цена продажи (от 0.01 до 1000000)
     * @param float $volume Количество голды для продажи
     * @return array Ответ от API с результатом продажи
     * @throws ApiException При ошибке запроса или неверных параметрах
     */
    public function sell(float $price, float $volume): array
    {
        if ($price < 0.01 || $price > 1000000) {
            throw new ApiException('Цена должна быть от 0.01 до 1000000');
        }

        if ($volume <= 0) {
            throw new ApiException('Объем должен быть больше 0');
        }

        $params = [
            'price' => $price,
            'volume' => $volume
        ];

        $response = $this->makeRequest('trade/sell', $params);

        // API может возвращать данные напрямую или в обертке result
        if (isset($response['result']) && is_array($response['result'])) {
            $result = $response['result'];
        } elseif (is_array($response) && isset($response['done_volume'])) {
            // Если данные пришли напрямую, оборачиваем в result
            $result = $response;
            $response = ['result' => $result];
        } else {
            throw new ApiException('Неожиданный ответ от API: ' . json_encode($response));
        }

        // Проверяем структуру ответа
        if (!isset($result['done_volume']) || !isset($result['sweets_received'])) {
            throw new ApiException('Неверная структура ответа: ' . json_encode($response));
        }

        return $response;
    }

    /**
     * Получить список заявок бота на Ирис-бирже
     * 
     * @return array Ответ от API со списком заявок
     * @throws ApiException При ошибке запроса
     */
    public function getMyOrders(): array
    {
        $response = $this->makeRequest('trade/my_orders');

        if (!isset($response['result']) || !is_array($response['result'])) {
            throw new ApiException('Неожиданный ответ от API: ' . json_encode($response));
        }

        // Проверяем структуру ответа
        $result = $response['result'];
        
        // Если результат пустой массив, возвращаем структуру с пустыми массивами
        if (empty($result)) {
            return [
                'result' => [
                    'buy' => [],
                    'sell' => []
                ]
            ];
        }
        
        // Нормализуем структуру ответа
        $normalizedResult = [
            'buy' => $result['buy'] ?? [],
            'sell' => $result['sell'] ?? []
        ];
        
        // Проверяем, что хотя бы один из ключей существует
        if (empty($result['buy'] ?? []) && empty($result['sell'] ?? [])) {
            throw new ApiException('Неверная структура ответа: ' . json_encode($response));
        }
        
        return ['result' => $normalizedResult];
    }

    /**
     * Отменить все заявки по указанной цене
     * 
     * @param float $price Цена для отмены заявок (от 0.01 до 1000000)
     * @return array Ответ от API с результатом отмены
     * @throws ApiException При ошибке запроса или неверных параметрах
     */
    public function cancelByPrice(float $price): array
    {
        if ($price < 0.01 || $price > 1000000) {
            throw new ApiException('Цена должна быть от 0.01 до 1000000');
        }

        $params = ['price' => $price];
        $response = $this->makeRequest('trade/cancel_price', $params);

        if (!isset($response['result']) || !is_array($response['result'])) {
            throw new ApiException('Неожиданный ответ от API: ' . json_encode($response));
        }

        // Проверяем структуру ответа
        $result = $response['result'];
        if (!isset($result['gold']) || !isset($result['sweets'])) {
            throw new ApiException('Неверная структура ответа: ' . json_encode($response));
        }

        return $response;
    }

    /**
     * Отменить все заявки бота
     * 
     * @return array Ответ от API с результатом отмены
     * @throws ApiException При ошибке запроса
     */
    public function cancelAll(): array
    {
        $response = $this->makeRequest('trade/cancel_all');

        if (!isset($response['result']) || !is_array($response['result'])) {
            throw new ApiException('Неожиданный ответ от API: ' . json_encode($response));
        }

        // Проверяем структуру ответа
        $result = $response['result'];
        if (!isset($result['gold']) || !isset($result['sweets'])) {
            throw new ApiException('Неверная структура ответа: ' . json_encode($response));
        }

        return $response;
    }

    /**
     * Отменить выбранную заявку частично
     * 
     * @param int $id ID заявки на Ирис-бирже
     * @param float $volume Объем голды для отмены
     * @return array Ответ от API с результатом отмены
     * @throws ApiException При ошибке запроса или неверных параметрах
     */
    public function cancelPart(int $id, float $volume): array
    {
        if ($id <= 0) {
            throw new ApiException('ID заявки должен быть больше 0');
        }

        if ($volume <= 0) {
            throw new ApiException('Объем должен быть больше 0');
        }

        $params = [
            'id' => $id,
            'volume' => $volume
        ];

        $response = $this->makeRequest('trade/cancel_part', $params);

        if (!isset($response['result']) || !is_array($response['result'])) {
            throw new ApiException('Неожиданный ответ от API: ' . json_encode($response));
        }

        // Проверяем структуру ответа
        $result = $response['result'];
        if (!isset($result['gold']) || !isset($result['sweets'])) {
            throw new ApiException('Неверная структура ответа: ' . json_encode($response));
        }

        return $response;
    }

    /**
     * Получить только заявки на покупку
     * 
     * @return array Массив заявок на покупку
     * @throws ApiException При ошибке запроса
     */
    public function getBuyOrders(): array
    {
        $orders = $this->getMyOrders();
        return $orders['result']['buy'] ?? [];
    }

    /**
     * Получить только заявки на продажу
     * 
     * @return array Массив заявок на продажу
     * @throws ApiException При ошибке запроса
     */
    public function getSellOrders(): array
    {
        $orders = $this->getMyOrders();
        return $orders['result']['sell'] ?? [];
    }

    /**
     * Получить общий объем заявок на покупку
     * 
     * @return float Общий объем заявок на покупку
     * @throws ApiException При ошибке запроса
     */
    public function getTotalBuyVolume(): float
    {
        $buyOrders = $this->getBuyOrders();
        return array_sum(array_column($buyOrders, 'volume'));
    }

    /**
     * Получить общий объем заявок на продажу
     * 
     * @return float Общий объем заявок на продажу
     * @throws ApiException При ошибке запроса
     */
    public function getTotalSellVolume(): float
    {
        $sellOrders = $this->getSellOrders();
        return array_sum(array_column($sellOrders, 'volume'));
    }

    /**
     * Получить количество активных заявок
     * 
     * @return array Массив с количеством заявок
     * @throws ApiException При ошибке запроса
     */
    public function getOrdersCount(): array
    {
        $orders = $this->getMyOrders();
        return [
            'buy_count' => count($orders['result']['buy']),
            'sell_count' => count($orders['result']['sell']),
            'total_count' => count($orders['result']['buy']) + count($orders['result']['sell'])
        ];
    }

    /**
     * Проверить, есть ли активные заявки
     * 
     * @return bool true если есть активные заявки, false иначе
     * @throws ApiException При ошибке запроса
     */
    public function hasActiveOrders(): bool
    {
        $count = $this->getOrdersCount();
        return $count['total_count'] > 0;
    }
}
