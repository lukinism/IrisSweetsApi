<?php

namespace IrisSweetsApi\Api\Sweets;

use IrisSweetsApi\Http\HttpClient;

class Sweets
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
     * Выдать ириски пользователю
     * 
     * @param int|float $sweets Количество ирисок для отправки
     * @param int $userId ID пользователя
     * @param string $comment Комментарий к отправке
     * @return array Ответ от API {"result": int} при успехе
 * @throws \IrisSweetsApi\Exception\ApiException При ошибке запроса или неверных параметрах
     */
    public function give(int|float $sweets, int $userId, string $comment = ''): array
    {
        $giveSweets = new GiveSweets($this->httpClient, $this->botId, $this->irisToken);
        return $giveSweets->give($sweets, $userId, $comment);
    }

    /**
     * Получить историю операций с ирисками
     * 
     * @param int $offset ID записи для смещения (по умолчанию 0 - с начала)
     * @return array Ответ от API с историей операций с ирисками
     * @throws \IrisSweetsApi\Exception\ApiException При ошибке запроса или неверных параметрах
     */
    public function getHistory(int $offset = 0): array
    {
        $getSweetsTransactions = new GetSweetsHistory($this->httpClient, $this->botId, $this->irisToken);
        return $getSweetsTransactions->getHistory($offset);
    }
}
