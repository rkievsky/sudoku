<?php

namespace Exceptions;

class GameError extends BasicError
{
    const MAX_PLAYERS_EXCEEDED = 1;
    const PLAYER_NOT_REGISTRED = 2;

    /**
     * @inheritDoc
     */
    protected static function getMessageByCode(int $code, string $additional = null)
    {
        $message = 'Неизвестная ошибка';
        switch ($code) {
            case self::MAX_PLAYERS_EXCEEDED:
                $message = 'Превышено максимальное количество игроков';
                break;
            case self::PLAYER_NOT_REGISTRED:
                $message = 'Игрок не зарегистрирован';
                break;
        }

        return $message;
    }
}