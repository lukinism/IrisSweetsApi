<?php

namespace IrisSweetsApi\Api\Nft;

use IrisSweetsApi\Api\AbstractApi;
use IrisSweetsApi\Http\HttpClient;
use IrisSweetsApi\Exception\ApiException;

class GetNftList extends AbstractApi
{
    public function __construct(HttpClient $httpClient, string $botId = '', string $irisToken = '')
    {
        parent::__construct($httpClient, $botId, $irisToken);
    }

    /**
     * Получить список NFT
     * 
     * @param int $limit Количество записей в ответе (по умолчанию 200)
     * @param int $offset Смещение для пагинации (по умолчанию 0)
     * @return array Ответ от API с массивом NFT
     * @throws ApiException При ошибке запроса или неверных параметрах
     */
    public function getList(int $limit = 200, int $offset = 0): array
    {
        if ($limit <= 0) {
            throw new ApiException('Limit должен быть больше 0');
        }

        if ($offset < 0) {
            throw new ApiException('Offset должен быть больше или равен 0');
        }

        $params = [
            'limit' => $limit,
            'offset' => $offset
        ];

        $response = $this->makeRequest('nft/list', $params);

        if (!isset($response['result']) || !is_array($response['result'])) {
            throw new ApiException('Неожиданный ответ от API: ' . json_encode($response));
        }

        return $response;
    }
}

