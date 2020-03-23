<?php

namespace Classes\Game;

class Sudoku
{
    const SIZE = 9;

    /** @var Cell[][]  */
    private $field = null;

    /** @var string $gameId  */
    public $gameId = null;

    /**
     * @return array
     */
    private function generateField()
    {
        // надоело это тестовое задание уже. Не буду генератор прикручивать.
        return [
            ['x' => 1, 'y' => 2, 'value' => 9],
            ['x' => 1, 'y' => 4, 'value' => 8],
            ['x' => 1, 'y' => 6, 'value' => 4],
            ['x' => 1, 'y' => 8, 'value' => 1],
            ['x' => 2, 'y' => 1, 'value' => 4],
            ['x' => 2, 'y' => 4, 'value' => 1],
            ['x' => 2, 'y' => 6, 'value' => 3],
            ['x' => 2, 'y' => 9, 'value' => 7],
            ['x' => 4, 'y' => 1, 'value' => 6],
            ['x' => 4, 'y' => 3, 'value' => 9],
            ['x' => 4, 'y' => 7, 'value' => 8],
            ['x' => 4, 'y' => 9, 'value' => 4],
            ['x' => 5, 'y' => 1, 'value' => 3],
            ['x' => 5, 'y' => 5, 'value' => 9],
            ['x' => 5, 'y' => 9, 'value' => 5],
            ['x' => 6, 'y' => 4, 'value' => 4],
            ['x' => 6, 'y' => 6, 'value' => 5],
            ['x' => 7, 'y' => 2, 'value' => 3],
            ['x' => 7, 'y' => 3, 'value' => 8],
            ['x' => 7, 'y' => 4, 'value' => 7],
            ['x' => 7, 'y' => 6, 'value' => 2],
            ['x' => 7, 'y' => 7, 'value' => 6],
            ['x' => 7, 'y' => 8, 'value' => 9],
            ['x' => 8, 'y' => 3, 'value' => 1],
            ['x' => 8, 'y' => 4, 'value' => 5],
            ['x' => 8, 'y' => 6, 'value' => 8],
            ['x' => 8, 'y' => 7, 'value' => 2],
            ['x' => 9, 'y' => 5, 'value' => 3],
        ];
    }

    /**
     * Sudoku constructor.
     *
     * @param string $id
     */
    public function __construct(string $id)
    {
        $this->gameId = $id;
    }

    /**
     * @param Coords[] $coords
     */
    public function fillField(array $coords)
    {
        foreach ($coords as $mark) {
            $this->field[$mark['x']][$mark['y']] = new Cell($mark['x'], $mark['y'], $mark['value']);
        }
    }

    public function newGame()
    {
        $this->fillField($this->generateField());
    }

    /**
     * @return array
     */
    public function getField(): array
    {
        $result = [];
        foreach ($this->field as $x => $row) {
            foreach ($row as $y => $cell) {
                /** @var Cell $cell */

                $player = $cell->getPlayer();

                $result[] = [
                    'x'      => $cell->getX(),
                    'y'      => $cell->getY(),
                    'value'  => $cell->getValue(),
                    'player' => $player ?? 0,
                ];
            }
        }

        return $result;
    }

    public function isGameOver()
    {

    }
}