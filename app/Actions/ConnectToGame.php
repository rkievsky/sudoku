<?php

namespace Actions;

use Classes\Server;
use Requests\BasicRQ;
use Responses\BasicRS;
use Requests\ConnectToGameRQ;
use Responses\ConnectToGameRS;

class ConnectToGame extends BasicAction
{
    const PRIMITIVE = 'connectToGame';

    /**
     * @inheritDoc
     */
    public function makeRQ(\stdClass $raw = null): BasicRQ
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
        $response = new ConnectToGameRS($this->getPrimitive(), $this->server->getGameId());
        $response->host = Server::HOST_NAME;
        $response->port = Server::MASTER_PORT;

        $response->player = $this->server->players->addPlayer($request->name);
        $response->field = $this->server->sudoku->getField();

        return $response;
    }
}