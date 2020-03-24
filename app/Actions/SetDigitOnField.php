<?php

namespace Actions;

use Requests\BasicRQ;
use Requests\SetDigitOnFieldRQ;
use Responses\BasicRS;
use Responses\SetDigitOnFieldRS;

class SetDigitOnField extends BasicAction
{
    const PRIMITIVE = 'setDigitOnField';

    /**
     * @inheritDoc
     */
    public function makeRQ(\stdClass $raw = null): BasicRQ
    {
        return SetDigitOnFieldRQ::create($raw);
    }

    /**
     *
     * @param SetDigitOnFieldRQ $request
     * @return SetDigitOnFieldRS
     * @throws \Exceptions\BasicError
     */
    public function handle(BasicRQ $request): BasicRS
    {
        $response = new SetDigitOnFieldRS($this->getPrimitive(), $this->server->getGameId());

        if ($this->server->sudoku->setDigit(
            $request->x,
            $request->y,
            $request->value,
            $this->server->players->getPlayer($request->player)
        )) {
            $response->field = [[
                'x'      => $request->x,
                'y'      => $request->y,
                'value'  => $request->value,
                'player' => $request->player,
            ]];

            if ($response->gameOver = $this->server->sudoku->isGameOver()) {
                $this->server->players->saveWinner($request->player);
            }

            $this->server->setNeedToNotifyListeners(true);
        }

        return $response;
    }
}