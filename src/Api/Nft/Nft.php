<?php

namespace IrisSweetsApi\Api\Nft;

use IrisSweetsApi\Http\HttpClient;

class Nft
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
     * Выдать NFT пользователю
     * 
     * @param int $id ID NFT в системе ириса
     * @param string $name Название гифта из адреса t.me/nft/* (например, PlusPepe-1)
     * @param int $userId ID пользователя
     * @param string $comment Комментарий к выдаче (опционально)
     * @return array Ответ от API {"result": int} при успехе
     * @throws \IrisSweetsApi\Exception\ApiException При ошибке запроса или неверных параметрах
     */
    public function give(int $id, string $name, int $userId, string $comment = ''): array
    {
        $giveNft = new GiveNft($this->httpClient, $this->botId, $this->irisToken);
        return $giveNft->give($id, $name, $userId, $comment);
    }

    /**
     * Получить информацию о NFT с учётом видимости владельца
     * 
     * @param int $id ID NFT в системе ириса
     * @param string $name Название гифта из адреса t.me/nft/* (например, PlusPepe-1)
     * @return array Ответ от API с информацией о NFT
     * @throws \IrisSweetsApi\Exception\ApiException При ошибке запроса или неверных параметрах
     */
    public function getInfo(int $id, string $name): array
    {
        $getNftInfo = new GetNftInfo($this->httpClient, $this->botId, $this->irisToken);
        return $getNftInfo->getInfo($id, $name);
    }

    /**
     * Получить список NFT
     * 
     * @param int $limit Количество записей в ответе (по умолчанию 200)
     * @param int $offset Смещение для пагинации (по умолчанию 0)
     * @return array Ответ от API с массивом NFT
     * @throws \IrisSweetsApi\Exception\ApiException При ошибке запроса или неверных параметрах
     */
    public function getList(int $limit = 200, int $offset = 0): array
    {
        $getNftList = new GetNftList($this->httpClient, $this->botId, $this->irisToken);
        return $getNftList->getList($limit, $offset);
    }

    /**
     * Получить историю операций с NFT
     * 
     * @param int $limit Количество записей в ответе (по умолчанию 200)
     * @param int $offset Смещение для пагинации (по умолчанию 0)
     * @return array Ответ от API с историей операций с NFT
     * @throws \IrisSweetsApi\Exception\ApiException При ошибке запроса или неверных параметрах
     */
    public function getHistory(int $limit = 200, int $offset = 0): array
    {
        $getNftHistory = new GetNftHistory($this->httpClient, $this->botId, $this->irisToken);
        return $getNftHistory->getHistory($limit, $offset);
    }
}

