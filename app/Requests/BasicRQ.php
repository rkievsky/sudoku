<?php

namespace Requests;

use Exceptions\RequestError;

class BasicRQ
{
    /** @var string $gameId */
    public $gameId = null;

    /** @var string $action  */
    public $action = null;

    /**
     * @param string $raw
     *
     * @return BasicRQ
     * @throws \Exceptions\BasicError
     */
    public static function create(string $raw = null): BasicRQ
    {
        $rq = new static();
        $rq->collect($raw);

        return $rq;
    }

    /**
     * собирает данные запроса из окружения
     *
     * @return \stdClass возвращает JSON, для дальнейщего извлечения параметров
     *
     * @throws \Exceptions\BasicError
     */
    public function collect(string $raw = null): \stdClass
    {
        if (is_null($raw)) {
            return new \stdClass();
        }

        $raw = json_decode($raw);
        $this->gameId = $raw->gameId;
        $this->action = $raw->action;

        return $raw;
    }
}