<?php

namespace Classes\Game;

class Sudoku
{
    const SIZE = 9;

    /** @var Cell[][]  */
    private $field = null;

    /** @var string $gameId  */
    public $gameId = null;

    /** @var bool $gameOver */
    public $gameOver = false;

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
        $this->fillField($this->generateField());
    }

    /**
     * @param array $coords
     */
    public function fillField(array $coords)
    {
        foreach ($coords as $mark) {
            $this->field[$mark['x']][$mark['y']] = new Cell($mark['x'], $mark['y'], $mark['value']);
        }
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
                /** @var Player $player */
                $player = $cell->getPlayer();
                $result[] = [
                    'x'      => $cell->getX(),
                    'y'      => $cell->getY(),
                    'value'  => $cell->getValue(),
                    'player' => $player ? $player->getId() : 0,
                ];
            }
        }

        return $result;
    }

    public function isGameOver(): bool
    {
        // проверим строки
        for ($i = 1; $i <= self::SIZE; $i++) {
            $digits = [];
            for ($j = 1; $j <= self::SIZE; $j++) {
                if (empty($this->field[$i][$j])) {
                    return false;
                }

                $cell = $this->field[$i][$j];
                $digits[$cell->getValue()] = ($digits[$cell->getValue()] ?? 0) + 1;
            }
            if (count($digits) !== 9) {
                return false;
            }
        }

        // проверим столбцы
        for ($j = 1; $j <= self::SIZE; $j++) {
            $digits = [];
            for ($i = 1; $i <= self::SIZE; $i++) {
                if (empty($this->field[$i][$j])) {
                    return false;
                }

                $cell = $this->field[$i][$j];
                $digits[$cell->getValue()] = ($digits[$cell->getValue()] ?? 0) + 1;
            }
            if (count($digits) !== 9) {
                return false;
            }
        }

        // проверим квадраты
        for ($i = 1; $i <= self::SIZE; $i += 3) {
            for ($j = 1; $j <= self::SIZE; $j += 3) {
                $digits = [];
                for ($ii = $i; $ii <= $i + 3; $ii++) {
                    for ($jj = $j; $jj <= $j + 3; $jj++) {
                        if (empty($this->field[$i][$j])) {
                            return false;
                        }

                        $cell = $this->field[$ii][$jj];
                        $digits[$cell->getValue()] = ($digits[$cell->getValue()] ?? 0) + 1;
                    }
                }
                if (count($digits) !== 9) {
                    return false;
                }
            }
        }

        return true;
    }

    public function setDigit(int $x, int $y, int $value, Player $player = null): bool
    {
        if (!(
            empty($this->field[$x][$y])
            || !$this->field[$x][$y]->getPlayer()
            || ($player && $this->field[$x][$y]->getPlayer() === $player)
        )) {
            return false;
        }

        $this->field[$x][$y] = new Cell($x, $y, $value, $player);

        return true;
    }
}