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
     * @param \stdClass $raw
     *
     * @return BasicRQ
     * @throws \Exceptions\BasicError
     */
    final public static function create(\stdClass $raw = null): BasicRQ
    {
        try {
            $rq = new static();
            $rq->collect($raw);
        } catch (\Throwable $error) {
            throw RequestError::create(RequestError::CANT_COLLECT_REQUEST_DATA, null, $error);
        }

        return $rq;
    }

    /**
     * собирает данные запроса из окружения
     *
     * @return \stdClass возвращает JSON, для дальнейщего извлечения параметров
     *
     * @throws \Exceptions\BasicError
     */
    public function collect(\stdClass $raw = null): \stdClass
    {
        $this->gameId = $raw->gameId;
        $this->action = $raw->action;

        return $raw;
    }
}