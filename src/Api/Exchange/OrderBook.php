<?php

namespace IrisSweetsApi\Api\Exchange;

use IrisSweetsApi\Http\HttpClient;
use IrisSweetsApi\Exception\ApiException;

class OrderBook
{
    private HttpClient $httpClient;
    private string $baseUrl;

    public function __construct(HttpClient $httpClient, string $baseUrl = 'https://iris-tg.ru/k/trade/')
    {
        $this->httpClient = $httpClient;
        $this->baseUrl = rtrim($baseUrl, '/');
    }

    /**
     * Получить стакан заявок
     * 
     * @return array Массив с ключами 'buy' и 'sell', содержащий заявки на покупку и продажу
     * @throws ApiException
     */
    public function getOrderBook(): array
    {
        $url = $this->baseUrl . '/order_book';
        
        try {
            $response = $this->httpClient->get($url);
            $data = json_decode($response, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new ApiException('Ошибка декодирования JSON ответа: ' . json_last_error_msg());
            }
            
            if (!isset($data['buy']) || !isset($data['sell'])) {
                throw new ApiException('Неверный формат ответа API: отсутствуют ключи buy или sell');
            }
            
            return $data;
        } catch (ApiException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new ApiException('Ошибка при получении стакана заявок: ' . $e->getMessage());
        }
    }

    /**
     * Получить только заявки на покупку
     * 
     * @return array Массив заявок на покупку
     * @throws ApiException
     */
    public function getBuyOrders(): array
    {
        $orderBook = $this->getOrderBook();
        return $orderBook['buy'] ?? [];
    }

    /**
     * Получить только заявки на продажу
     * 
     * @return array Массив заявок на продажу
     * @throws ApiException
     */
    public function getSellOrders(): array
    {
        $orderBook = $this->getOrderBook();
        return $orderBook['sell'] ?? [];
    }

    /**
     * Получить лучшую цену покупки (самая высокая цена среди заявок на покупку)
     * 
     * @return float|null Лучшая цена покупки или null, если заявок нет
     * @throws ApiException
     */
    public function getBestBidPrice(): ?float
    {
        $buyOrders = $this->getBuyOrders();
        
        if (empty($buyOrders)) {
            return null;
        }
        
        $maxPrice = max(array_column($buyOrders, 'price'));
        return (float) $maxPrice;
    }

    /**
     * Получить лучшую цену продажи (самая низкая цена среди заявок на продажу)
     * 
     * @return float|null Лучшая цена продажи или null, если заявок нет
     * @throws ApiException
     */
    public function getBestAskPrice(): ?float
    {
        $sellOrders = $this->getSellOrders();
        
        if (empty($sellOrders)) {
            return null;
        }
        
        $minPrice = min(array_column($sellOrders, 'price'));
        return (float) $minPrice;
    }

    /**
     * Получить спред (разница между лучшей ценой продажи и лучшей ценой покупки)
     * 
     * @return float|null Спред или null, если нет заявок
     * @throws ApiException
     */
    public function getSpread(): ?float
    {
        $bestBid = $this->getBestBidPrice();
        $bestAsk = $this->getBestAskPrice();
        
        if ($bestBid === null || $bestAsk === null) {
            return null;
        }
        
        return $bestAsk - $bestBid;
    }

    /**
     * Получить общий объем заявок на покупку
     * 
     * @return float Общий объем заявок на покупку
     * @throws ApiException
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
     * @throws ApiException
     */
    public function getTotalSellVolume(): float
    {
        $sellOrders = $this->getSellOrders();
        return array_sum(array_column($sellOrders, 'volume'));
    }

    /**
     * Получить заявки на покупку, отсортированные по цене (от высокой к низкой)
     * 
     * @return array Заявки на покупку, отсортированные по цене
     * @throws ApiException
     */
    public function getBuyOrdersSorted(): array
    {
        $buyOrders = $this->getBuyOrders();
        usort($buyOrders, function($a, $b) {
            return $b['price'] <=> $a['price'];
        });
        return $buyOrders;
    }

    /**
     * Получить заявки на продажу, отсортированные по цене (от низкой к высокой)
     * 
     * @return array Заявки на продажу, отсортированные по цене
     * @throws ApiException
     */
    public function getSellOrdersSorted(): array
    {
        $sellOrders = $this->getSellOrders();
        usort($sellOrders, function($a, $b) {
            return $a['price'] <=> $b['price'];
        });
        return $sellOrders;
    }

    /**
     * Найти заявки в определенном диапазоне цен
     * 
     * @param float $minPrice Минимальная цена
     * @param float $maxPrice Максимальная цена
     * @return array Массив с ключами 'buy' и 'sell', содержащий заявки в диапазоне
     * @throws ApiException
     */
    public function getOrdersInPriceRange(float $minPrice, float $maxPrice): array
    {
        $orderBook = $this->getOrderBook();
        
        $filteredBuy = array_filter($orderBook['buy'], function($order) use ($minPrice, $maxPrice) {
            return $order['price'] >= $minPrice && $order['price'] <= $maxPrice;
        });
        
        $filteredSell = array_filter($orderBook['sell'], function($order) use ($minPrice, $maxPrice) {
            return $order['price'] >= $minPrice && $order['price'] <= $maxPrice;
        });
        
        return [
            'buy' => array_values($filteredBuy),
            'sell' => array_values($filteredSell)
        ];
    }
}




