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
     * @throws \Exceptions\BasicError
     */
    public function __construct(string $id)
    {
        $this->gameId = $id;
        // добавим игрока-заглушку. От его имени будут расставлены первоначальный цифры
        $this->addPlayer('Fake Player');
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
        $player->setId(count($this->players) - 1);

        return $player->getId();
    }

    /**
     * @param int $id
     * @return Player
     * @throws \Exceptions\BasicError
     */
    public function getPlayer(int $id): Player
    {
        if (!$id || !array_key_exists($id, $this->players)) {
            throw GameError::create(GameError::PLAYER_NOT_REGISTRED);
        }

        return $this->players[$id];
    }

    public function delPlayer(string $name)
    {
        foreach ($this->players as $num => $player) {
            if ($player->getName() == $name) {
                unset($this->players[$num]);
                break;
            }
        }
    }

    /**
     * @param int $id
     * @throws \Exceptions\BasicError
     */
    public function saveWinner(int $id): void
    {
        $winner = $this->getPlayer($id)->getName();
        exec("echo '$winner' >  top.txt");
    }

    public function getWinners(): string
    {
        return file_get_contents('top.txt');
    }
}