<?php

namespace IrisSweetsApi\Api\Exchange;

use IrisSweetsApi\Http\HttpClient;
use IrisSweetsApi\Exception\ApiException;

class Deals
{
    private HttpClient $httpClient;
    private string $baseUrl;

    public function __construct(HttpClient $httpClient, string $baseUrl = 'https://iris-tg.ru/trade/')
    {
        $this->httpClient = $httpClient;
        $this->baseUrl = rtrim($baseUrl, '/');
    }

    /**
     * Получить последние сделки
     * 
     * @param int|null $fromId Минимальный ID сделки (опционально)
     * @return array Массив сделок
     * @throws ApiException
     */
    public function getDeals(?int $fromId = null): array
    {
        $url = $this->baseUrl . '/deals';
        
        $params = [];
        if ($fromId !== null) {
            $params['id'] = $fromId;
        }
        
        try {
            $response = $this->httpClient->get($url, $params);
            $data = json_decode($response, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new ApiException('Ошибка декодирования JSON ответа: ' . json_last_error_msg());
            }
            
            if (!is_array($data)) {
                throw new ApiException('Неверный формат ответа API: ожидается массив');
            }
            
            return $data;
        } catch (ApiException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new ApiException('Ошибка при получении сделок: ' . $e->getMessage());
        }
    }

    /**
     * Получить сделки за последние N минут
     * 
     * @param int $minutes Количество минут
     * @return array Массив сделок за указанный период
     * @throws ApiException
     */
    public function getDealsForLastMinutes(int $minutes): array
    {
        $deals = $this->getDeals();
        $cutoffTime = time() - ($minutes * 60);
        
        return array_filter($deals, function($deal) use ($cutoffTime) {
            return $deal['date'] >= $cutoffTime;
        });
    }

    /**
     * Получить сделки за последние N часов
     * 
     * @param int $hours Количество часов
     * @return array Массив сделок за указанный период
     * @throws ApiException
     */
    public function getDealsForLastHours(int $hours): array
    {
        return $this->getDealsForLastMinutes($hours * 60);
    }

    /**
     * Получить сделки за последние N дней
     * 
     * @param int $days Количество дней
     * @return array Массив сделок за указанный период
     * @throws ApiException
     */
    public function getDealsForLastDays(int $days): array
    {
        return $this->getDealsForLastHours($days * 24);
    }

    /**
     * Получить только сделки на покупку
     * 
     * @param int|null $fromId Минимальный ID сделки (опционально)
     * @return array Массив сделок на покупку
     * @throws ApiException
     */
    public function getBuyDeals(?int $fromId = null): array
    {
        $deals = $this->getDeals($fromId);
        return array_filter($deals, function($deal) {
            return $deal['type'] === 'buy';
        });
    }

    /**
     * Получить только сделки на продажу
     * 
     * @param int|null $fromId Минимальный ID сделки (опционально)
     * @return array Массив сделок на продажу
     * @throws ApiException
     */
    public function getSellDeals(?int $fromId = null): array
    {
        $deals = $this->getDeals($fromId);
        return array_filter($deals, function($deal) {
            return $deal['type'] === 'sell';
        });
    }

    /**
     * Получить сделки в определенном диапазоне цен
     * 
     * @param float $minPrice Минимальная цена
     * @param float $maxPrice Максимальная цена
     * @param int|null $fromId Минимальный ID сделки (опционально)
     * @return array Массив сделок в диапазоне цен
     * @throws ApiException
     */
    public function getDealsInPriceRange(float $minPrice, float $maxPrice, ?int $fromId = null): array
    {
        $deals = $this->getDeals($fromId);
        return array_filter($deals, function($deal) use ($minPrice, $maxPrice) {
            return $deal['price'] >= $minPrice && $deal['price'] <= $maxPrice;
        });
    }

    /**
     * Получить сделки с объемом больше указанного
     * 
     * @param int $minVolume Минимальный объем
     * @param int|null $fromId Минимальный ID сделки (опционально)
     * @return array Массив сделок с большим объемом
     * @throws ApiException
     */
    public function getDealsWithMinVolume(int $minVolume, ?int $fromId = null): array
    {
        $deals = $this->getDeals($fromId);
        return array_filter($deals, function($deal) use ($minVolume) {
            return $deal['volume'] >= $minVolume;
        });
    }

    /**
     * Получить статистику по сделкам
     * 
     * @param int|null $fromId Минимальный ID сделки (опционально)
     * @return array Статистика по сделкам
     * @throws ApiException
     */
    public function getDealsStats(?int $fromId = null): array
    {
        $deals = $this->getDeals($fromId);
        
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
        
        $buyDeals = array_filter($deals, function($deal) { return $deal['type'] === 'buy'; });
        $sellDeals = array_filter($deals, function($deal) { return $deal['type'] === 'sell'; });
        
        $prices = array_column($deals, 'price');
        $volumes = array_column($deals, 'volume');
        
        $totalValue = array_sum(array_map(function($deal) {
            return $deal['price'] * $deal['volume'];
        }, $deals));
        
        return [
            'total_deals' => count($deals),
            'total_volume' => array_sum($volumes),
            'total_value' => $totalValue,
            'avg_price' => $totalValue / array_sum($volumes),
            'min_price' => min($prices),
            'max_price' => max($prices),
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
     * @param int|null $fromId Минимальный ID сделки (опционально)
     * @return array Массив последних сделок
     * @throws ApiException
     */
    public function getLastDeals(int $limit, ?int $fromId = null): array
    {
        $deals = $this->getDeals($fromId);
        return array_slice($deals, 0, $limit);
    }

    /**
     * Получить сделки, отсортированные по цене
     * 
     * @param string $order 'asc' для возрастания, 'desc' для убывания
     * @param int|null $fromId Минимальный ID сделки (опционально)
     * @return array Отсортированные сделки
     * @throws ApiException
     */
    public function getDealsSortedByPrice(string $order = 'desc', ?int $fromId = null): array
    {
        $deals = $this->getDeals($fromId);
        
        usort($deals, function($a, $b) use ($order) {
            if ($order === 'asc') {
                return $a['price'] <=> $b['price'];
            } else {
                return $b['price'] <=> $a['price'];
            }
        });
        
        return $deals;
    }

    /**
     * Получить сделки, отсортированные по объему
     * 
     * @param string $order 'asc' для возрастания, 'desc' для убывания
     * @param int|null $fromId Минимальный ID сделки (опционально)
     * @return array Отсортированные сделки
     * @throws ApiException
     */
    public function getDealsSortedByVolume(string $order = 'desc', ?int $fromId = null): array
    {
        $deals = $this->getDeals($fromId);
        
        usort($deals, function($a, $b) use ($order) {
            if ($order === 'asc') {
                return $a['volume'] <=> $b['volume'];
            } else {
                return $b['volume'] <=> $a['volume'];
            }
        });
        
        return $deals;
    }

    /**
     * Получить сделки, отсортированные по времени
     * 
     * @param string $order 'asc' для возрастания, 'desc' для убывания
     * @param int|null $fromId Минимальный ID сделки (опционально)
     * @return array Отсортированные сделки
     * @throws ApiException
     */
    public function getDealsSortedByTime(string $order = 'desc', ?int $fromId = null): array
    {
        $deals = $this->getDeals($fromId);
        
        usort($deals, function($a, $b) use ($order) {
            if ($order === 'asc') {
                return $a['date'] <=> $b['date'];
            } else {
                return $b['date'] <=> $a['date'];
            }
        });
        
        return $deals;
    }

    /**
     * Найти сделки по группе
     * 
     * @param int $groupId ID группы
     * @param int|null $fromId Минимальный ID сделки (опционально)
     * @return array Массив сделок группы
     * @throws ApiException
     */
    public function getDealsByGroup(int $groupId, ?int $fromId = null): array
    {
        $deals = $this->getDeals($fromId);
        return array_filter($deals, function($deal) use ($groupId) {
            return $deal['group_id'] === $groupId;
        });
    }
}
