<?php

namespace Requests;

use Exceptions\RequestError;

class ConnectToGameRQ extends BasicRQ
{
    /** @var string $name  */
    public $name = null;

    /** @inheritDoc */
    public function collect(string $raw = null): \stdClass
    {
        try {
            $json = parent::collect($raw);
            $this->name = $json->name;
        } catch (\Throwable $error) {
            throw RequestError::create(RequestError::CANT_COLLECT_REQUEST_DATA, null, $error);
        }

        return $json;
    }
}