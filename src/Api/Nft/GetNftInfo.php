<?php

namespace IrisSweetsApi\Api\Nft;

use IrisSweetsApi\Api\AbstractApi;
use IrisSweetsApi\Http\HttpClient;
use IrisSweetsApi\Exception\ApiException;

class GetNftInfo extends AbstractApi
{
    public function __construct(HttpClient $httpClient, string $botId = '', string $irisToken = '')
    {
        parent::__construct($httpClient, $botId, $irisToken);
    }

    /**
     * Получить информацию о NFT с учётом видимости владельца
     * 
     * @param int $id ID NFT в системе ириса
     * @param string $name Название гифта из адреса t.me/nft/* (например, PlusPepe-1)
     * @return array Ответ от API с информацией о NFT
     * @throws ApiException При ошибке запроса или неверных параметрах
     */
    public function getInfo(int $id, string $name): array
    {
        if ($id <= 0) {
            throw new ApiException('ID NFT должен быть больше 0');
        }

        if (empty($name)) {
            throw new ApiException('Название NFT не может быть пустым');
        }

        $params = [
            'id' => $id,
            'name' => $name
        ];

        $response = $this->makeRequest('nft/info', $params);

        if (!isset($response['result']) || !is_array($response['result'])) {
            throw new ApiException('Неожиданный ответ от API: ' . json_encode($response));
        }

        return $response;
    }
}

