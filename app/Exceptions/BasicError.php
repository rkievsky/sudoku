<?php

namespace Exceptions;

abstract class BasicError extends \Exception
{
    const UNKNOWN_ERROR = 0;

    /**
     * Возвращает сообщение об ошибке по её коду
     *
     * @param int         $code
     * @param string|null $additional
     * @return mixed
     */
    abstract protected static function getMessageByCode(int $code, string $additional = null);

    /**
     * Создаёт исключение
     *
     * @param int             $code
     * @param string|null     $additional
     * @param \Throwable|null $prev
     * @return static
     */
    public static function create(int $code, string $additional = null, \Throwable $prev = null)
    {
        return new static(static::getMessageByCode($code, $additional), $code, $prev);
    }
}