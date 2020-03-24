<?php

namespace Actions;

use Classes\Server;
use Requests\BasicRQ;
use Responses\BasicRS;

abstract class BasicAction
{
    const PRIMITIVE = "It's fake!";

    /** @var Server $server  */
    protected $server = null;

    /**
     * Создаёт запрос
     *
     * @param \stdClass $raw
     *
     * @return BasicRQ
     *
     * @throws \Exceptions\BasicError
     */
    abstract public function makeRQ(\stdClass $raw = null): BasicRQ;

    /**
     * Выполняет обработку запроса
     *
     * @param BasicRQ $request
     *
     * @return BasicRS
     *
     * @throws \Exceptions\BasicError
     */
    abstract public function handle(BasicRQ $request): BasicRS;

    /**
     * @return string
     */
    final public function getPrimitive(): string
    {
        return static::PRIMITIVE;
    }

    public function __construct(Server $server)
    {
        $this->server = $server;
    }
}