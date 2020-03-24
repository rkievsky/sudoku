<?php

namespace Requests;

class SetDigitOnFieldRQ extends BasicRQ
{
    /** @var int $x  */
    public $x = null;

    /** @var int $y  */
    public $y = null;

    /** @var int $value  */
    public $value = null;

    /** @var int $player  */
    public $player = null;

    /**
     * @inheritDoc
     */
    public function collect(\stdClass $raw = null): \stdClass
    {
        $json = parent::collect($raw);
        $this->x = $json->x;
        $this->y = $json->y;
        $this->value = $json->value;
        $this->player = $json->player;

        return $json;
    }
}