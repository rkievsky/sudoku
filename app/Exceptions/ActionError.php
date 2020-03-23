<?php

namespace Exceptions;

class ActionError extends BasicError
{
    const UNKNOWN_ACTION = 1;

    /**
     * @inheritDoc
     */
    protected static function getMessageByCode(int $code, string $additional = null)
    {
        $message = 'Неизвестная ошибка';
        switch ($code) {
            case self::UNKNOWN_ACTION:
                $message = 'Запрошенного действия не существует';
        }

        return $message;
    }
}