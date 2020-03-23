<?php

namespace Actions;

use Requests\BasicRQ;
use Responses\BasicRS;
use Requests\ConnectToGameRQ;
use Responses\ConnectToGameRS;

class ConnectToGame implements IAction
{
    /**
     * @inheritDoc
     */
    public function makeRQ(): BasicRQ
    {
        return ConnectToGameRQ::create();
    }

    /**
     * @param ConnectToGameRQ $request
     * @return ConnectToGameRS
     * @throws \Exceptions\BasicError
     */
    public function handle(BasicRQ $request): BasicRS
    {
        $response = new ConnectToGameRS();
        $response->id = game()->getId();
        $response->player = game()->players->addPlayer($request->name);
        $response->field = game()->sudoku->getField();

        return $response;
    }
}