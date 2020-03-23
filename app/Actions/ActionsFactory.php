<?php

namespace Actions;

use Exceptions\ActionError;

class ActionsFactory
{
    /**
     * Создаёт классы действий
     *
     * @return IAction
     * @throws \Exceptions\BasicError
     */
    public function create(string $action) : IAction
    {
        switch ($action) {
            case 'startNew':
                return $this->createStartNewGame();
                break;
            case 'connect':
                return $this->createConnect();
                break;
            case 'connectToGame':
                return $this->createConnectToGame();
                break;
            case 'setDigitOnField':
                return $this->createSetDigitOnField();
                break;
            case 'showTop':
                return $this->createShowPlayersTop();
                break;
            default:
                throw ActionError::create(ActionError::UNKNOWN_ACTION);
        }
    }

    /**
     * @return StartNewGame
     */
    public function createStartNewGame(): StartNewGame
    {
        return new StartNewGame();
    }

    /**
     * @return ConnectToGame
     */
    public function createConnect(): Connect
    {
        return new Connect();
    }

    /**
     * @return ConnectToGame
     */
    public function createConnectToGame(): ConnectToGame
    {
        return new ConnectToGame();
    }

    /**
     * @return SetDigitOnField
     */
    public function createSetDigitOnField(): SetDigitOnField
    {
        return new SetDigitOnField();
    }

    /**
     * @return ShowPlayersTop
     */
    public function createShowPlayersTop(): ShowPlayersTop
    {
        return new ShowPlayersTop();
    }
}