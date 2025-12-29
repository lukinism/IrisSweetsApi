<?php

namespace IrisSweetsApi\Api\Nft;

use IrisSweetsApi\Api\AbstractApi;
use IrisSweetsApi\Http\HttpClient;
use IrisSweetsApi\Exception\ApiException;

class GiveNft extends AbstractApi
{
    public function __construct(HttpClient $httpClient, string $botId = '', string $irisToken = '')
    {
        parent::__construct($httpClient, $botId, $irisToken);
    }

    /**
     * Выдать NFT пользователю
     * 
     * @param int $id ID NFT в системе ириса
     * @param string $name Название гифта из адреса t.me/nft/* (например, PlusPepe-1)
     * @param int $userId ID пользователя
     * @param string $comment Комментарий к выдаче (опционально)
     * @return array Ответ от API {"result": int} при успехе
     * @throws ApiException При ошибке запроса или неверных параметрах
     */
    public function give(int $id, string $name, int $userId, string $comment = ''): array
    {
        if ($id <= 0) {
            throw new ApiException('ID NFT должен быть больше 0');
        }

        if (empty($name)) {
            throw new ApiException('Название NFT не может быть пустым');
        }

        if ($userId <= 0) {
            throw new ApiException('ID пользователя должен быть больше 0');
        }

        $params = [
            'id' => $id,
            'name' => $name,
            'user_id' => $userId
        ];

        if (!empty($comment)) {
            $params['comment'] = $comment;
        }

        $response = $this->makeRequest('nft/give', $params);

        if (!isset($response['result']) || !is_int($response['result'])) {
            throw new ApiException('Неожиданный ответ от API: ' . json_encode($response));
        }

        return $response;
    }
}

