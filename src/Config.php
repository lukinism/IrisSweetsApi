<?php

namespace IrisSweetsApi;

class Config
{
    private static ?Config $instance = null;
    private array $config = [];
    private string $envFile;

    private function __construct()
    {
        $this->envFile = dirname(__DIR__) . '/.env';
        $this->loadEnvironment();
    }

    public static function getInstance(): Config
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Загружает переменные окружения из .env файла
     */
    private function loadEnvironment(): void
    {
        if (!file_exists($this->envFile)) {
            return;
        }

        $lines = file($this->envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            if (strpos($line, '#') === 0) {
                continue;
            }

            if (strpos($line, '=') !== false) {
                [$key, $value] = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value, '"\'');

                if (($value[0] === '"' && $value[-1] === '"') || 
                    ($value[0] === "'" && $value[-1] === "'")) {
                    $value = substr($value, 1, -1);
                }
                
                $this->config[$key] = $value;
            }
        }
    }

    /**
     * Получить значение конфигурации
     */
    public function get(string $key, string $default = ''): string
    {
        return $this->config[$key] ?? $default;
    }

    /**
     * Установить значение конфигурации
     */
    public function set(string $key, string $value): void
    {
        $this->config[$key] = $value;
    }

    /**
     * Проверить, существует ли ключ конфигурации
     */
    public function has(string $key): bool
    {
        return isset($this->config[$key]);
    }

    /**
     * Получить Bot ID
     */
    public function getBotId(): string
    {
        return $this->get('IRIS_BOT_ID', '');
    }

    /**
     * Получить Iris Token
     */
    public function getIrisToken(): string
    {
        return $this->get('IRIS_TOKEN', '');
    }

    /**
     * Получить Base URL (фиксированный для v0.2)
     */
    public function getBaseUrl(): string
    {
        return 'https://iris-tg.ru/api/v0.2/';
    }

    /**
     * Получить прокси (опционально)
     */
    public function getProxy(): ?string
    {
        $proxy = $this->get('PROXY', '');
        return $proxy !== '' ? $proxy : null;
    }

    /**
     * Проверить, что все необходимые параметры установлены
     */
    public function validate(): bool
    {
        return !empty($this->getBotId()) && !empty($this->getIrisToken());
    }

    /**
     * Получить все параметры конфигурации
     */
    public function getAll(): array
    {
        return $this->config;
    }
}

