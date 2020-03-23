<?php

namespace Requests;

abstract class BasicRQ
{
    /** @var string $gameId */
    public $gameId = null;

    /**
     * @return BasicRQ
     * @throws \Exceptions\BasicError
     */
    public static function create(): BasicRQ
    {
        $rq = new static();
        $rq->collect();

        return $rq;
    }

    /**
     * собирает данные запроса из окружения
     *
     * @throws \Exceptions\BasicError
     */
    abstract public function collect(): void;
}