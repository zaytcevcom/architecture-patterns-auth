<?php

declare(strict_types=1);

namespace App\Components\Validator;

final class Regex
{
    public const string FIRST_NAME = '/^[а-яёА-ЯЁa-zA-Z]+$/iu';
    public const string LAST_NAME  = '/^[а-яёА-ЯЁa-zA-Z]+$/iu';

    public static function firstName(): string
    {
        return self::FIRST_NAME;
    }

    public static function lastName(): string
    {
        return self::LAST_NAME;
    }
}
