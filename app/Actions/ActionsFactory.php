<?php

namespace Actions;

use Classes\Server;
use Exceptions\ActionError;

class ActionsFactory
{
    /**
     * Создаёт классы действий
     *
     * @return BasicAction
     * @throws \Exceptions\BasicError
     */
    public function create(string $action, Server $server) : BasicAction
    {
        switch ($action) {
            case 'connectToGame':
                return new ConnectToGame($server);
                break;
            case 'setDigitOnField':
                return new SetDigitOnField($server);
                break;
            case 'showTop':
                return new ShowPlayersTop($server);
                break;
            default:
                throw ActionError::create(ActionError::UNKNOWN_ACTION);
        }
    }
}