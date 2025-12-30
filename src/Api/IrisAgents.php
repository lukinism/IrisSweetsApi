<?php

namespace IrisSweetsApi\Api;

use IrisSweetsApi\Http\HttpClient;

class IrisAgents extends AbstractApi
{
    public function __construct(HttpClient $httpClient, string $botId = '', string $irisToken = '')
    {
        parent::__construct($httpClient, $botId, $irisToken);
    }

    /**
     * Получить список действующих агентов Ириса
     * 
     * @return array Массив идентификаторов действующих агентов
     * @throws \IrisSweetsApi\Exception\ApiException При ошибке запроса
     */
    public function getAgents(): array
    {
        return $this->makeRequest('iris_agents');
    }
}

