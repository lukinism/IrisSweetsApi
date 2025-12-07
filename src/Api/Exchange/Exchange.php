<?php

namespace IrisSweetsApi\Api\Exchange;

use IrisSweetsApi\Http\HttpClient;

class Exchange
{
    private HttpClient $httpClient;
    private string $botId;
    private string $irisToken;

    public function __construct(HttpClient $httpClient, string $botId = '', string $irisToken = '')
    {
        $this->httpClient = $httpClient;
        $this->botId = $botId;
        $this->irisToken = $irisToken;
    }

    /**
     * Получить объект для работы со стаканом заявок
     * 
     * @return OrderBook
     */
    public function orderBook(): OrderBook
    {
        return new OrderBook($this->httpClient, $this->botId, $this->irisToken);
    }

    /**
     * Получить стакан заявок (быстрый доступ)
     * 
     * @return array Массив с ключами 'buy' и 'sell'
     */
    public function getOrderBook(): array
    {
        return $this->orderBook()->getOrderBook();
    }

    /**
     * Получить заявки на покупку (быстрый доступ)
     * 
     * @return array Массив заявок на покупку
     */
    public function getBuyOrders(): array
    {
        return $this->orderBook()->getBuyOrders();
    }

    /**
     * Получить заявки на продажу (быстрый доступ)
     * 
     * @return array Массив заявок на продажу
     */
    public function getSellOrders(): array
    {
        return $this->orderBook()->getSellOrders();
    }

    /**
     * Получить лучшую цену покупки (быстрый доступ)
     * 
     * @return float|null Лучшая цена покупки или null
     */
    public function getBestBidPrice(): ?float
    {
        return $this->orderBook()->getBestBidPrice();
    }

    /**
     * Получить лучшую цену продажи (быстрый доступ)
     * 
     * @return float|null Лучшая цена продажи или null
     */
    public function getBestAskPrice(): ?float
    {
        return $this->orderBook()->getBestAskPrice();
    }

    /**
     * Получить спред (быстрый доступ)
     * 
     * @return float|null Спред или null
     */
    public function getSpread(): ?float
    {
        return $this->orderBook()->getSpread();
    }

    /**
     * Получить объект для работы с историей сделок
     * 
     * @return Deals
     */
    public function deals(): Deals
    {
        return new Deals($this->httpClient, $this->botId, $this->irisToken);
    }

    /**
     * Получить последние сделки (быстрый доступ)
     * 
     * @param int $id ID сделки, начиная с которой будет выдано limit записей (по умолчанию 0)
     * @param int $limit Максимальное количество выдаваемых записей (от 0 до 200, по умолчанию 200)
     * @return array Массив сделок
     */
    public function getDeals(int $id = 0, int $limit = 200): array
    {
        return $this->deals()->getDeals($id, $limit);
    }

    /**
     * Получить статистику по сделкам (быстрый доступ)
     * 
     * @param int $id ID сделки для начала выборки (по умолчанию 0)
     * @param int $limit Максимальное количество записей (по умолчанию 200)
     * @return array Статистика по сделкам
     */
    public function getDealsStats(int $id = 0, int $limit = 200): array
    {
        return $this->deals()->getDealsStats($id, $limit);
    }
}
