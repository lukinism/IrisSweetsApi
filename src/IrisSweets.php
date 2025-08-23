<?php

namespace IrisSweetsApi;

use IrisSweetsApi\Http\HttpClient;
use IrisSweetsApi\Api\Balance;
use IrisSweetsApi\Api\Sweets\Sweets;
use IrisSweetsApi\Api\Gold\Gold;
use IrisSweetsApi\Api\Pocket;
use IrisSweetsApi\Config;


class IrisSweets
{
    private HttpClient $client;
    private Config $config;

    public function __construct(?string $botId = null, ?string $irisToken = null, ?string $apiVersion = null)
    {
        $this->config = Config::getInstance();

        if ($botId !== null) {
            $this->config->set('IRIS_BOT_ID', $botId);
        }
        if ($irisToken !== null) {
            $this->config->set('IRIS_TOKEN', $irisToken);
        }
        if ($apiVersion !== null) {
            $this->config->set('IRIS_API_VERSION', $apiVersion);
        }

        if (!$this->config->validate()) {
            throw new \InvalidArgumentException(
                'Необходимо установить IRIS_BOT_ID и IRIS_TOKEN. ' .
                'Создайте файл .env на основе .env.example или передайте параметры в конструктор.'
            );
        }
        
        $headers = [
            'Accept' => 'application/json'
        ];

        $this->client = new HttpClient($headers);
    }

    public function balance(): Balance
    {
        return new Balance($this->client, $this->config->getBotId(), $this->config->getIrisToken(), $this->config->getBaseUrl(), $this->config->getApiVersion());
    }

    public function sweets(): Sweets
    {
        return new Sweets($this->client, $this->config->getBotId(), $this->config->getIrisToken(), $this->config->getBaseUrl(), $this->config->getApiVersion());
    }

    public function gold(): Gold
    {
        return new Gold($this->client, $this->config->getBotId(), $this->config->getIrisToken(), $this->config->getBaseUrl(), $this->config->getApiVersion());
    }

    public function pocket(): Pocket
    {
        return new Pocket($this->client, $this->config->getBotId(), $this->config->getIrisToken(), $this->config->getBaseUrl(), $this->config->getApiVersion());
    }

    /**
     * Установить версию API для всех последующих запросов
     */
    public function setApiVersion(string $version): void
    {
        $this->config->setApiVersion($version);
    }

    /**
     * Получить текущую версию API
     */
    public function getApiVersion(): string
    {
        return $this->config->getApiVersion();
    }
}
