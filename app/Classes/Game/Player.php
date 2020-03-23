<?php

namespace Classes\Game;

class Player
{
    /** @var int $id  */
    private $id = null;

    /** @var string $name  */
    private $name = null;

    /** @var float $lastTurn  */
    private $lastTurn = null;

    public function __construct($name)
    {
        $this->setName($name);
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return float
     */
    public function getLastTurn(): float
    {
        return $this->lastTurn;
    }

    /**
     * @param float $microtime
     */
    public function setLastTurn($microtime = null): void
    {
        $this->lastTurn = $microtime ?: microtime(true);
    }
}