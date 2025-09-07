<?php

namespace IrisSweetsApi\Api;

use IrisSweetsApi\Http\HttpClient;
use IrisSweetsApi\Exception\ApiException;
use IrisSweetsApi\Exception\RetryHandler;
use IrisSweetsApi\Exception\ErrorHandler;

abstract class AbstractApi
{
    protected HttpClient $httpClient;
    protected string $baseUrl;
    protected string $botId;
    protected string $irisToken;
    protected RetryHandler $retryHandler;

    public function __construct(HttpClient $httpClient, string $botId = '', string $irisToken = '', string $baseUrl = 'https://iris-tg.ru/api/v0.2/')
    {
        $this->httpClient = $httpClient;
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->botId = $botId;
        $this->irisToken = $irisToken;
        $this->retryHandler = new RetryHandler();
    }

    protected function makeRequest(string $method, array $params = []): array
    {
        return $this->retryHandler->execute(
            fn() => $this->executeRequest($method, $params),
            ['method' => $method, 'params' => $params]
        );
    }

    /**
     * Выполнить HTTP запрос
     */
    private function executeRequest(string $method, array $params = []): array
    {
        $url = $this->buildUrl($method);
        
        try {
            $response = $this->httpClient->get($url, $params);
        } catch (ApiException $e) {
            ErrorHandler::logError($e);
            throw $e;
        }

        $decodedResponse = json_decode($response, true) ?: [];

        if (isset($decodedResponse['error'])) {
            $exception = new ApiException(
                $decodedResponse['error']['description'] ?? 'API Error',
                $decodedResponse['error']['code'] ?? 0,
                null,
                $decodedResponse
            );

            ErrorHandler::logError($exception);
            throw $exception;
        }

        return $decodedResponse;
    }

    protected function buildUrl(string $method): string
    {
        $token = "{$this->botId}_{$this->irisToken}";
        return "{$this->baseUrl}/{$token}/{$method}";
    }

    /**
     * Установить настройки повторных попыток
     */
    public function setRetrySettings(int $maxRetries, int $baseDelay = 1, float $backoffMultiplier = 2.0): void
    {
        $this->retryHandler->setMaxRetries($maxRetries);
        $this->retryHandler->setBaseDelay($baseDelay);
        $this->retryHandler->setBackoffMultiplier($backoffMultiplier);
    }

    /**
     * Получить статистику повторных попыток
     */
    public function getRetryStats(): array
    {
        return $this->retryHandler->getStats();
    }

    /**
     * Включить режим отладки
     */
    public function enableDebug(): void
    {
        $this->httpClient->setDebug(true);
    }

    /**
     * Выключить режим отладки
     */
    public function disableDebug(): void
    {
        $this->httpClient->setDebug(false);
    }
}
