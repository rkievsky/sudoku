<?php

namespace Actions;

use Requests\BasicRQ;
use Responses\BasicRS;

interface IAction
{
    /**
     * Создаёт запрос
     *
     * @param string $raw
     *
     * @return BasicRQ
     *
     * @throws \Exceptions\BasicError
     */
    public function makeRQ(string $raw = null): BasicRQ;

    /**
     * @return string
     */
    public function getPrimitive(): string;

    /**
     * Выполняет обработку запроса
     *
     * @param BasicRQ $request
     *
     * @return BasicRS
     *
     * @throws \Exceptions\BasicError
     */
    public function handle(BasicRQ $request): BasicRS;
}