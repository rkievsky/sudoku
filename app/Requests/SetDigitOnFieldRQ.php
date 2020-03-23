<?php

namespace Requests;

use Exceptions\RequestError;

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
    public function collect(string $raw = null): \stdClass
    {
        try {
            $json = parent::collect($raw);
            $this->x = $json->x;
            $this->y = $json->y;
            $this->value = $json->value;
            $this->player = $json->player;
        } catch (\Throwable $error) {
            throw RequestError::create(RequestError::CANT_COLLECT_REQUEST_DATA, null, $error);
        }

        return $json;
    }
}