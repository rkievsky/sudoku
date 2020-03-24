<?php

namespace Requests;

class ConnectToGameRQ extends BasicRQ
{
    /** @var string $name  */
    public $name = null;

    /** @inheritDoc */
    public function collect(\stdClass $raw = null): \stdClass
    {
        $json = parent::collect($raw);
        $this->name = $json->name;

        return $json;
    }
}