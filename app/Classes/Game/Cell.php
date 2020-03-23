<?php

namespace Classes\Game;

/**
 * Class Cell
 *
 * @package Classes\Game
 */
class Cell
{
    /** @var int $x  */
    private $x = null;

    /** @var int $y  */
    private $y = null;

    /** @var Player $player  */
    private $player = null;

    /** @var int $value  */
    private $value = null;

    /**
     * Cell constructor.
     *
     * @param int $x
     * @param int $y
     * @param int $value
     */
    public function __construct(int $x, int $y, int $value, Player $player = null)
    {
        $this->x = $x;
        $this->y = $y;
        $this->value = $value;
        $this->player = $player;
    }

    /**
     * @return int
     */
    public function getX(): int
    {
        return $this->x;
    }

    /**
     * @return int
     */
    public function getY(): int
    {
        return $this->y;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
    }

    /**
     * @return Player
     */
    public function getPlayer(): ?Player
    {
        return $this->player;
    }

}