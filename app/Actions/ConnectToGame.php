<?php

namespace Actions;

use Classes\Server;
use Requests\BasicRQ;
use Responses\BasicRS;
use Requests\ConnectToGameRQ;
use Responses\ConnectToGameRS;

class ConnectToGame implements IAction
{
    /**
     * @inheritDoc
     */
    public function makeRQ(string $raw = null): BasicRQ
    {
        return ConnectToGameRQ::create($raw);
    }

    /**
     * @param ConnectToGameRQ $request
     * @return ConnectToGameRS
     * @throws \Exceptions\BasicError
     */
    public function handle(BasicRQ $request): BasicRS
    {
        $response = new ConnectToGameRS($this->getPrimitive(), game()->getId());
        $response->host = Server::HOST_NAME;
        $response->port = Server::MASTER_PORT;

        $response->player = game()->players->addPlayer($request->name);
        $response->field = game()->sudoku->getField();

        return $response;
    }

    /**
     * @inheritDoc
     */
    public function getPrimitive(): string
    {
        return 'connectToGame';
    }
}