<?php

namespace Actions;

use Requests\BasicRQ;
use Requests\SetDigitOnFieldRQ;
use Responses\BasicRS;
use Responses\SetDigitOnFieldRS;

class SetDigitOnField implements IAction
{
    /**
     * @inheritDoc
     */
    public function makeRQ(string $raw = null): BasicRQ
    {
        return SetDigitOnFieldRQ::create($raw);
    }

    /**
     * @inheritDoc
     */
    public function getPrimitive(): string
    {
        return 'setDigitOnField';
    }

    /**
     *
     * @param SetDigitOnFieldRQ $request
     * @return SetDigitOnFieldRS
     * @throws \Exceptions\BasicError
     */
    public function handle(BasicRQ $request): BasicRS
    {
        $response = new SetDigitOnFieldRS($this->getPrimitive(), game()->getId());

        if (game()->sudoku->setDigit($request->x, $request->y, $request->value, game()->players->getPlayer($request->player))) {
            $response->field = [[
                'x'      => $request->x,
                'y'      => $request->y,
                'value'  => $request->value,
                'player' => $request->player,
            ]];
        }

        return $response;
    }
}