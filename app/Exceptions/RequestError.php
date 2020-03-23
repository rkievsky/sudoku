<?php

namespace Exceptions;

class RequestError extends BasicError
{
    const CANT_COLLECT_REQUEST_DATA = 1;

    /**
     * @inheritDoc
     */
    protected static function getMessageByCode(int $code, string $additional = null)
    {
        $message = 'Неизвестная ошибка';
        switch ($code) {
            case self::CANT_COLLECT_REQUEST_DATA:
                $message = 'Ошибка сбора данных запроса';
        }

        return $message;
    }
}