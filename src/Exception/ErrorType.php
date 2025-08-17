<?php

namespace IrisSweetsApi\Exception;

/**
 * Константы для типов ошибок API Iris Sweets
 */
class ErrorType
{
    // Ошибки валидации (400)
    public const SWEETS_GOLD_ZERO = 'SWEETS_GOLD_ZERO';
    public const INVALID_PARAMETERS = 'INVALID_PARAMETERS';
    
    // Ошибки авторизации (401)
    public const UNAUTHORIZED = 'UNAUTHORIZED';
    public const INVALID_TOKEN = 'INVALID_TOKEN';
    
    // Ошибки доступа (403)
    public const ACCOUNT_NOT_USER = 'ACCOUNT_NOT_USER';
    public const INSUFFICIENT_PERMISSIONS = 'INSUFFICIENT_PERMISSIONS';
    
    // Ошибки "не найдено" (404)
    public const USER_NOT_FOUND = 'USER_NOT_FOUND';
    public const RESOURCE_NOT_FOUND = 'RESOURCE_NOT_FOUND';
    
    // Ошибки конфликтов (409)
    public const NOT_ENOUGH_RESOURCES = 'NOT_ENOUGH_RESOURCES';
    public const UNSUCCESSFUL_DECREASE = 'UNSUCCESSFUL_DECREASE';
    public const OPERATION_FAILED = 'OPERATION_FAILED';
    
    // Серверные ошибки (500)
    public const CALCULATION_ERROR = 'CALCULATION_ERROR';
    public const INTERNAL_ERROR = 'INTERNAL_ERROR';
    
    /**
     * Получить тип ошибки из описания API
     */
    public static function fromApiDescription(string $description): string
    {
        $descLower = strtolower($description);
        
        return match(true) {
            str_contains($descLower, 'sweets/gold is 0') => self::SWEETS_GOLD_ZERO,
            str_contains($descLower, 'user not found') => self::USER_NOT_FOUND,
            str_contains($descLower, 'account is not user') => self::ACCOUNT_NOT_USER,
            str_contains($descLower, 'ошибка в расчёте сжигании ирисок') => self::CALCULATION_ERROR,
            str_contains($descLower, 'not enough') => self::NOT_ENOUGH_RESOURCES,
            str_contains($descLower, 'unsuccessful') => self::UNSUCCESSFUL_DECREASE,
            default => self::INVALID_PARAMETERS,
        };
    }
    
    /**
     * Получить человекочитаемое описание типа ошибки
     */
    public static function getDescription(string $errorType): string
    {
        return match($errorType) {
            self::SWEETS_GOLD_ZERO => 'Количество ирисок/голды равно нулю',
            self::INVALID_PARAMETERS => 'Некорректные параметры запроса',
            self::UNAUTHORIZED => 'Не авторизован',
            self::INVALID_TOKEN => 'Неверный токен',
            self::ACCOUNT_NOT_USER => 'Аккаунт не является пользователем',
            self::INSUFFICIENT_PERMISSIONS => 'Недостаточно прав',
            self::USER_NOT_FOUND => 'Пользователь не найден',
            self::RESOURCE_NOT_FOUND => 'Ресурс не найден',
            self::NOT_ENOUGH_RESOURCES => 'Недостаточно ресурсов',
            self::UNSUCCESSFUL_DECREASE => 'Неудачное уменьшение ресурсов',
            self::OPERATION_FAILED => 'Операция не выполнена',
            self::CALCULATION_ERROR => 'Ошибка в расчетах',
            self::INTERNAL_ERROR => 'Внутренняя ошибка',
            default => 'Неизвестная ошибка',
        };
    }
}
