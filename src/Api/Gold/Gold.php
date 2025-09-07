<?php

namespace IrisSweetsApi\Api\Gold;

use IrisSweetsApi\Http\HttpClient;

class Gold
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
     * Выдать голду пользователю
     * 
     * @param int $gold Количество голды для отправки
     * @param int $userId ID пользователя
     * @param string $comment Комментарий к отправке
     * @return array Ответ от API {"result": int} при успехе
     * @throws \IrisSweetsApi\Exception\ApiException При ошибке запроса или неверных параметрах
     */
    public function give(int $gold, int $userId, string $comment = ''): array
    {
        $giveGold = new GiveGold($this->httpClient, $this->botId, $this->irisToken);
        return $giveGold->give($gold, $userId, $comment);
    }

    /**
     * Получить историю операций с голдой
     * 
     * @param int $offset ID записи для смещения (по умолчанию 0 - с начала)
     * @return array Ответ от API с историей операций с голдой
     * @throws \IrisSweetsApi\Exception\ApiException При ошибке запроса или неверных параметрах
     */
    public function getHistory(int $offset = 0): array
    {
        $getGoldHistory = new GetGoldHistory($this->httpClient, $this->botId, $this->irisToken);
        return $getGoldHistory->getHistory($offset);
    }
}
