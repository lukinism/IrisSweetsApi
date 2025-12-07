<?php

namespace IrisSweetsApi;

use IrisSweetsApi\Http\HttpClient;
use IrisSweetsApi\Api\Balance;
use IrisSweetsApi\Api\Sweets\Sweets;
use IrisSweetsApi\Api\Gold\Gold;
use IrisSweetsApi\Api\TgStars\TgStars;
use IrisSweetsApi\Api\Pocket;
use IrisSweetsApi\Api\Updates\Updates;
use IrisSweetsApi\Api\IrisAgents;
use IrisSweetsApi\Api\Exchange\Exchange;
use IrisSweetsApi\Api\UserInfo;
use IrisSweetsApi\Api\Trade;
use IrisSweetsApi\Config;


class IrisSweets
{
    private HttpClient $client;
    private Config $config;

    public function __construct(?string $botId = null, ?string $irisToken = null)
    {
        $this->config = Config::getInstance();

        if ($botId !== null) {
            $this->config->set('IRIS_BOT_ID', $botId);
        }
        if ($irisToken !== null) {
            $this->config->set('IRIS_TOKEN', $irisToken);
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
        return new Balance($this->client, $this->config->getBotId(), $this->config->getIrisToken(), $this->config->getBaseUrl());
    }

    public function sweets(): Sweets
    {
        return new Sweets($this->client, $this->config->getBotId(), $this->config->getIrisToken(), $this->config->getBaseUrl());
    }

    public function gold(): Gold
    {
        return new Gold($this->client, $this->config->getBotId(), $this->config->getIrisToken(), $this->config->getBaseUrl());
    }

    public function tgStars(): TgStars
    {
        return new TgStars($this->client, $this->config->getBotId(), $this->config->getIrisToken(), $this->config->getBaseUrl());
    }

    public function pocket(): Pocket
    {
        return new Pocket($this->client, $this->config->getBotId(), $this->config->getIrisToken(), $this->config->getBaseUrl());
    }

    public function updates(): Updates
    {
        return new Updates($this->client, $this->config->getBotId(), $this->config->getIrisToken(), $this->config->getBaseUrl());
    }

    public function irisAgents(): IrisAgents
    {
        return new IrisAgents($this->client, $this->config->getBotId(), $this->config->getIrisToken(), $this->config->getBaseUrl());
    }

    public function exchange(): Exchange
    {
        return new Exchange($this->client, $this->config->getBotId(), $this->config->getIrisToken());
    }

    public function userInfo(): UserInfo
    {
        return new UserInfo($this->client, $this->config->getBotId(), $this->config->getIrisToken(), $this->config->getBaseUrl());
    }

    public function trade(): Trade
    {
        return new Trade($this->client, $this->config->getBotId(), $this->config->getIrisToken(), $this->config->getBaseUrl());
    }
}
