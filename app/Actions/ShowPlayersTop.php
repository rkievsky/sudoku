<?php

namespace Actions;

use Requests\BasicRQ;
use Requests\ShowPlayersTopRQ;
use Responses\BasicRS;
use Responses\ShowPlayersTopRS;

class ShowPlayersTop extends BasicAction
{

    /**
     * @inheritDoc
     */
    public function makeRQ(\stdClass $raw = null): BasicRQ
    {
        return ShowPlayersTopRQ::create($raw);
    }

    /**
     * @inheritDoc
     */
    public function handle(BasicRQ $request): BasicRS
    {
        $response = new ShowPlayersTopRS($this->getPrimitive(), $this->server->game->getId());
        $response->content = $this->server->game->players->getWinners();

        return $response;
    }
}