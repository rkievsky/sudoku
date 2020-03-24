<?php

namespace Exceptions;

class ServerError extends BasicError
{
    const CANT_OPEN_SOCKET = 1;
    const SAME_USER_NAME = 2;

    /**
     * @inheritDoc
     */
    protected static function getMessageByCode(int $code, string $additional = null)
    {
        $message = 'Неизвестная ошибка';
        switch ($code) {
            case self::CANT_OPEN_SOCKET:
                $message = 'Не удалось открыть сокет';
                break;
            case self::SAME_USER_NAME:
                $message = 'Пользователь с таким именем уже играет';
                break;
        }

        return $message;
    }
}