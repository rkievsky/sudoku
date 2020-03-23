<?php

namespace Classes\Game;

class Game
{
    /** @var string $id  */
    private $id = null;

    /** @var Sudoku $sudoku */
    public $sudoku = null;

    /** @var Players  */
    public $players = null;

    /**
     * Game constructor.
     *
     * @param string $id
     * @throws \Exceptions\BasicError
     */
    public function __construct()
    {
        $this->id = getGUID();
        $this->sudoku = new Sudoku($this->id);
        $this->players = new Players($this->id);
    }

    public function getId(): string
    {
        return $this->id;
    }
}