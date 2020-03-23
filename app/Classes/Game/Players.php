<?php

namespace Classes\Game;

use Exceptions\GameError;

class Players
{
    const MAX_PLAYERS = 9;

    /** @var Player[] $players  */
    private $players = [];

    /** @var string $gameId  */
    public $gameId = null;

    /**
     * Players constructor.
     *
     * @param string $id
     */
    public function __construct(string $id)
    {
        $this->gameId = $id;
    }

    public function newGame()
    {

    }

    /**
     * Добавляет нового игрока в игру
     *
     * @param string $name
     *
     * @return int
     *
     * @throws \Exceptions\BasicError
     */
    public function addPlayer(string $name): int
    {
        if (count($this->players) >= self::MAX_PLAYERS) {
            throw GameError::create(GameError::MAX_PLAYERS_EXCEEDED);
        }

        $player = new Player($name);
        $this->players[] = $player;
        $player->setId(count($this->players));

        return $player->getId();
    }

    /**
     * @param int $id
     * @throws \Exceptions\BasicError
     */
    public function saveTurn($id)
    {
        if (!array_key_exists($id, $this->players)) {
            throw GameError::create(GameError::PLAYER_NOT_REGISTRED);
        }

        $this->players[$id]->setLastTurn();
    }
}