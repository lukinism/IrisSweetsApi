<?php

namespace IrisSweetsApi\Api\Exchange;

use IrisSweetsApi\Api\AbstractApi;
use IrisSweetsApi\Http\HttpClient;
use IrisSweetsApi\Exception\ApiException;

class Deals extends AbstractApi
{
    public function __construct(HttpClient $httpClient, string $botId = '', string $irisToken = '')
    {
        parent::__construct($httpClient, $botId, $irisToken);
    }

    /**
     * Получить историю сделок Ирис-биржи
     * 
     * @param int $id ID сделки, начиная с которой будет выдано limit записей (по умолчанию 0 - последние limit сделок)
     * @param int $limit Максимальное количество выдаваемых записей (от 0 до 200, по умолчанию 200)
     * @return array Массив сделок
     * @throws ApiException
     */
    public function getDeals(int $id = 0, int $limit = 200): array
    {
        if ($id < 0) {
            throw new ApiException('ID сделки не может быть отрицательным');
        }

        if ($limit < 0 || $limit > 200) {
            throw new ApiException('Limit должен быть от 0 до 200');
        }

        $params = [];
        
        if ($id > 0) {
            $params['id'] = $id;
        }
        
        if ($limit !== 200) {
            $params['limit'] = $limit;
        }

        $response = $this->makeRequest('trade/deals', $params);
        
        // API возвращает массив сделок напрямую или в обертке result
        if (isset($response['result']) && is_array($response['result'])) {
            return $response['result'];
        } elseif (is_array($response) && !isset($response['error'])) {
            // Если данные пришли напрямую как массив
            return $response;
        } else {
            throw new ApiException('Неверный формат ответа API: ожидается массив сделок');
        }
    }

    /**
     * Получить сделки за последние N минут
     * 
     * @param int $minutes Количество минут
     * @param int $id ID сделки для начала выборки (по умолчанию 0)
     * @param int $limit Максимальное количество записей (по умолчанию 200)
     * @return array Массив сделок за указанный период
     * @throws ApiException
     */
    public function getDealsForLastMinutes(int $minutes, int $id = 0, int $limit = 200): array
    {
        $deals = $this->getDeals($id, $limit);
        $cutoffTime = time() - ($minutes * 60);
        
        return array_filter($deals, function($deal) use ($cutoffTime) {
            return isset($deal['date']) && $deal['date'] >= $cutoffTime;
        });
    }

    /**
     * Получить сделки за последние N часов
     * 
     * @param int $hours Количество часов
     * @param int $id ID сделки для начала выборки (по умолчанию 0)
     * @param int $limit Максимальное количество записей (по умолчанию 200)
     * @return array Массив сделок за указанный период
     * @throws ApiException
     */
    public function getDealsForLastHours(int $hours, int $id = 0, int $limit = 200): array
    {
        return $this->getDealsForLastMinutes($hours * 60, $id, $limit);
    }

    /**
     * Получить сделки за последние N дней
     * 
     * @param int $days Количество дней
     * @param int $id ID сделки для начала выборки (по умолчанию 0)
     * @param int $limit Максимальное количество записей (по умолчанию 200)
     * @return array Массив сделок за указанный период
     * @throws ApiException
     */
    public function getDealsForLastDays(int $days, int $id = 0, int $limit = 200): array
    {
        return $this->getDealsForLastHours($days * 24, $id, $limit);
    }

    /**
     * Получить только сделки на покупку
     * 
     * @param int $id ID сделки для начала выборки (по умолчанию 0)
     * @param int $limit Максимальное количество записей (по умолчанию 200)
     * @return array Массив сделок на покупку
     * @throws ApiException
     */
    public function getBuyDeals(int $id = 0, int $limit = 200): array
    {
        $deals = $this->getDeals($id, $limit);
        return array_filter($deals, function($deal) {
            return isset($deal['type']) && $deal['type'] === 'buy';
        });
    }

    /**
     * Получить только сделки на продажу
     * 
     * @param int $id ID сделки для начала выборки (по умолчанию 0)
     * @param int $limit Максимальное количество записей (по умолчанию 200)
     * @return array Массив сделок на продажу
     * @throws ApiException
     */
    public function getSellDeals(int $id = 0, int $limit = 200): array
    {
        $deals = $this->getDeals($id, $limit);
        return array_filter($deals, function($deal) {
            return isset($deal['type']) && $deal['type'] === 'sell';
        });
    }

    /**
     * Получить сделки в определенном диапазоне цен
     * 
     * @param float $minPrice Минимальная цена
     * @param float $maxPrice Максимальная цена
     * @param int $id ID сделки для начала выборки (по умолчанию 0)
     * @param int $limit Максимальное количество записей (по умолчанию 200)
     * @return array Массив сделок в диапазоне цен
     * @throws ApiException
     */
    public function getDealsInPriceRange(float $minPrice, float $maxPrice, int $id = 0, int $limit = 200): array
    {
        $deals = $this->getDeals($id, $limit);
        return array_filter($deals, function($deal) use ($minPrice, $maxPrice) {
            return isset($deal['price']) && $deal['price'] >= $minPrice && $deal['price'] <= $maxPrice;
        });
    }

    /**
     * Получить сделки с объемом больше указанного
     * 
     * @param int $minVolume Минимальный объем
     * @param int $id ID сделки для начала выборки (по умолчанию 0)
     * @param int $limit Максимальное количество записей (по умолчанию 200)
     * @return array Массив сделок с большим объемом
     * @throws ApiException
     */
    public function getDealsWithMinVolume(int $minVolume, int $id = 0, int $limit = 200): array
    {
        $deals = $this->getDeals($id, $limit);
        return array_filter($deals, function($deal) use ($minVolume) {
            return isset($deal['volume']) && $deal['volume'] >= $minVolume;
        });
    }

    /**
     * Получить статистику по сделкам
     * 
     * @param int $id ID сделки для начала выборки (по умолчанию 0)
     * @param int $limit Максимальное количество записей (по умолчанию 200)
     * @return array Статистика по сделкам
     * @throws ApiException
     */
    public function getDealsStats(int $id = 0, int $limit = 200): array
    {
        $deals = $this->getDeals($id, $limit);
        
        if (empty($deals)) {
            return [
                'total_deals' => 0,
                'total_volume' => 0,
                'total_value' => 0,
                'avg_price' => 0,
                'min_price' => 0,
                'max_price' => 0,
                'buy_deals' => 0,
                'sell_deals' => 0,
                'buy_volume' => 0,
                'sell_volume' => 0
            ];
        }
        
        $buyDeals = array_filter($deals, function($deal) { 
            return isset($deal['type']) && $deal['type'] === 'buy'; 
        });
        $sellDeals = array_filter($deals, function($deal) { 
            return isset($deal['type']) && $deal['type'] === 'sell'; 
        });
        
        $prices = array_column($deals, 'price');
        $volumes = array_column($deals, 'volume');
        
        $totalValue = array_sum(array_map(function($deal) {
            return ($deal['price'] ?? 0) * ($deal['volume'] ?? 0);
        }, $deals));
        
        $totalVolume = array_sum($volumes);
        
        return [
            'total_deals' => count($deals),
            'total_volume' => $totalVolume,
            'total_value' => $totalValue,
            'avg_price' => $totalVolume > 0 ? $totalValue / $totalVolume : 0,
            'min_price' => !empty($prices) ? min($prices) : 0,
            'max_price' => !empty($prices) ? max($prices) : 0,
            'buy_deals' => count($buyDeals),
            'sell_deals' => count($sellDeals),
            'buy_volume' => array_sum(array_column($buyDeals, 'volume')),
            'sell_volume' => array_sum(array_column($sellDeals, 'volume'))
        ];
    }

    /**
     * Получить последние N сделок
     * 
     * @param int $limit Количество сделок
     * @param int $id ID сделки для начала выборки (по умолчанию 0)
     * @return array Массив последних сделок
     * @throws ApiException
     */
    public function getLastDeals(int $limit, int $id = 0): array
    {
        $deals = $this->getDeals($id, $limit);
        return array_slice($deals, 0, $limit);
    }

    /**
     * Получить сделки, отсортированные по цене
     * 
     * @param string $order 'asc' для возрастания, 'desc' для убывания
     * @param int $id ID сделки для начала выборки (по умолчанию 0)
     * @param int $limit Максимальное количество записей (по умолчанию 200)
     * @return array Отсортированные сделки
     * @throws ApiException
     */
    public function getDealsSortedByPrice(string $order = 'desc', int $id = 0, int $limit = 200): array
    {
        $deals = $this->getDeals($id, $limit);
        
        usort($deals, function($a, $b) use ($order) {
            $priceA = $a['price'] ?? 0;
            $priceB = $b['price'] ?? 0;
            if ($order === 'asc') {
                return $priceA <=> $priceB;
            } else {
                return $priceB <=> $priceA;
            }
        });
        
        return $deals;
    }

    /**
     * Получить сделки, отсортированные по объему
     * 
     * @param string $order 'asc' для возрастания, 'desc' для убывания
     * @param int $id ID сделки для начала выборки (по умолчанию 0)
     * @param int $limit Максимальное количество записей (по умолчанию 200)
     * @return array Отсортированные сделки
     * @throws ApiException
     */
    public function getDealsSortedByVolume(string $order = 'desc', int $id = 0, int $limit = 200): array
    {
        $deals = $this->getDeals($id, $limit);
        
        usort($deals, function($a, $b) use ($order) {
            $volumeA = $a['volume'] ?? 0;
            $volumeB = $b['volume'] ?? 0;
            if ($order === 'asc') {
                return $volumeA <=> $volumeB;
            } else {
                return $volumeB <=> $volumeA;
            }
        });
        
        return $deals;
    }

    /**
     * Получить сделки, отсортированные по времени
     * 
     * @param string $order 'asc' для возрастания, 'desc' для убывания
     * @param int $id ID сделки для начала выборки (по умолчанию 0)
     * @param int $limit Максимальное количество записей (по умолчанию 200)
     * @return array Отсортированные сделки
     * @throws ApiException
     */
    public function getDealsSortedByTime(string $order = 'desc', int $id = 0, int $limit = 200): array
    {
        $deals = $this->getDeals($id, $limit);
        
        usort($deals, function($a, $b) use ($order) {
            $dateA = $a['date'] ?? 0;
            $dateB = $b['date'] ?? 0;
            if ($order === 'asc') {
                return $dateA <=> $dateB;
            } else {
                return $dateB <=> $dateA;
            }
        });
        
        return $deals;
    }

    /**
     * Найти сделки по группе (group_id)
     * Используется для оценки объёма крупных сделок, выходящих за пределы одного уровня цены
     * 
     * @param int $groupId ID корневой сделки (group_id)
     * @param int $id ID сделки для начала выборки (по умолчанию 0)
     * @param int $limit Максимальное количество записей (по умолчанию 200)
     * @return array Массив сделок группы
     * @throws ApiException
     */
    public function getDealsByGroup(int $groupId, int $id = 0, int $limit = 200): array
    {
        $deals = $this->getDeals($id, $limit);
        return array_filter($deals, function($deal) use ($groupId) {
            return isset($deal['group_id']) && $deal['group_id'] === $groupId;
        });
    }
}
