<?php

namespace Actions;

use Classes\Server;
use Requests\BasicRQ;
use Requests\ConnectRQ;
use Responses\BasicRS;
use Requests\ConnectToGameRQ;
use Responses\ConnectRS;
use Responses\ConnectToGameRS;

class Connect implements IAction
{
    /**
     * @inheritDoc
     */
    public function makeRQ(string $raw = null): BasicRQ
    {
        return ConnectRQ::create($raw);
    }

    /**
     * @param ConnectRQ $request
     * @return ConnectRS
     * @throws \Exceptions\BasicError
     */
    public function handle(BasicRQ $request): BasicRS
    {
        $response = new ConnectRS($this->getPrimitive(), game()->getId());
        $response->gameId = game()->getId();
        $response->host = Server::HOST_NAME;
        $response->port = Server::MASTER_PORT;

        return $response;
    }

    /**
     * @inheritDoc
     */
    public function getPrimitive(): string
    {
        return 'connect';
    }
}