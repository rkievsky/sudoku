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
     */
    public function __construct($id = null)
    {
        $newGame = false;
        if (!$id) {
            $id = getGUID();
            $newGame = true;
        }

        $this->sudoku = new Sudoku($id);
        $this->players = new Players($id);

        if ($newGame) {
            $this->sudoku->newGame();
            $this->players->newGame();
        } else {
            $this->sudoku->loadGame();
            $this->players->loadGame();
        }
    }

    public function getId()
    {
        return $this->id;
    }
}