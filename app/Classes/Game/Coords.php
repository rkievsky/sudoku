<?php

namespace classes\game;

class Coords
{
    /** @var int $x  */
    private $x = null;

    /** @var int $y  */
    private $y = null;

    /**
     * @return int
     */
    public function getX(): int
    {
        return $this->x;
    }

    /**
     * @param int $x
     */
    public function setX(int $x): void
    {
        $this->x = $x;
    }

    /**
     * @return int
     */
    public function getY(): int
    {
        return $this->y;
    }

    /**
     * @param int $y
     */
    public function setY(int $y): void
    {
        $this->y = $y;
    }

    public function __construct(int $x, int $y)
    {
    }
}