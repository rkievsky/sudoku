<?php

namespace Actions;

use Exceptions\ActionError;

class ActionsFactory
{
    /**
     * Возвращет имя запрощенного действия
     *
     * @return string
     */
    private function getActionName(): string
    {
        return trim($_SERVER['REQUEST_URI'], '/');
    }

    /**
     * Создаёт классы действий
     *
     * @return IAction
     * @throws \Exceptions\BasicError
     */
    public function create() : IAction
    {
        switch ($this->getActionName()) {
            case 'startNew':
                return $this->createStartNewGame();
                break;
            case 'connect':
                return $this->createConnectToGame();
                break;
            case 'setDigit':
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