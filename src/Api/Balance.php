<?php

namespace IrisSweetsApi\Api;

use IrisSweetsApi\Http\HttpClient;

class Balance extends AbstractApi
{
    public function __construct(HttpClient $httpClient, string $botId = '', string $irisToken = '')
    {
        parent::__construct($httpClient, $botId, $irisToken);
    }

    public function getBalance(): array
    {
        return $this->makeRequest('pocket/balance');
    }
}