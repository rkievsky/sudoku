<?php

namespace Actions;

use Requests\BasicRQ;
use Responses\BasicRS;

interface IAction
{
    /**
     * Создаёт запрос
     *
     * @return BasicRQ
     *
     * @throws \Exceptions\BasicError
     */
    public function makeRQ(): BasicRQ;

    /**
     * Выполняет обработку запроса
     *
     * @return BasicRS
     *
     * @throws \Exceptions\BasicError
     */
    public function handle(BasicRQ $request): BasicRS;
}