<?php

namespace IrisSweetsApi\Api\TgStars;

use IrisSweetsApi\Http\HttpClient;

class TgStars
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
     * Передать тг-звёзды другому пользователю
     * 
     * @param int $tgstars Количество тг-звёзд для передачи
     * @param int $userId ID пользователя, которому передаются тг-звёзды
     * @param string $comment Подпись к переводу (необязательный параметр)
     * @return array Ответ от API {"result": int} - ID транзакции
     * @throws \IrisSweetsApi\Exception\ApiException При ошибке запроса или неверных параметрах
     */
    public function give(int $tgstars, int $userId, string $comment = ''): array
    {
        $giveTgStars = new GiveTgStars($this->httpClient, $this->botId, $this->irisToken);
        return $giveTgStars->give($tgstars, $userId, $comment);
    }

    /**
     * Получить историю изменения тг-звёзд в мешке бота
     * 
     * @param int $offset Будут выданы записи с id >= offset (необязательный параметр, по умолчанию выдаются limit последних записей)
     * @param int $limit Количество записей в ответе (необязательный параметр, по умолчанию 200)
     * @return array Массив записей истории тг-звёзд
     * @throws \IrisSweetsApi\Exception\ApiException При ошибке запроса или неверных параметрах
     */
    public function getHistory(int $offset = 0, int $limit = 200): array
    {
        $getTgStarsHistory = new GetTgStarsHistory($this->httpClient, $this->botId, $this->irisToken);
        return $getTgStarsHistory->getHistory($offset, $limit);
    }

    /**
     * Покупка тг-звёзд за ириски
     * 
     * @param int $tgstars Количество тг-звёзд для покупки
     * @return array Ответ от API {"result": int} - ID транзакции
     * @throws \IrisSweetsApi\Exception\ApiException При ошибке запроса или неверных параметрах
     */
    public function buy(int $tgstars): array
    {
        $buyTgStars = new BuyTgStars($this->httpClient, $this->botId, $this->irisToken);
        return $buyTgStars->buy($tgstars);
    }

    /**
     * Оценка стоимости покупки тг-звёзд
     * Показывает, сколько ирисок требуется для покупки указанного количества тг-звёзд
     * 
     * @param int $tgstars Количество тг-звёзд для покупки
     * @return array Ответ от API {"result": {"tgstars": int, "sweets": int}}
     * @throws \IrisSweetsApi\Exception\ApiException При ошибке запроса или неверных параметрах
     */
    public function getPrice(int $tgstars): array
    {
        $getTgStarsPrice = new GetTgStarsPrice($this->httpClient, $this->botId, $this->irisToken);
        return $getTgStarsPrice->getPrice($tgstars);
    }
}

