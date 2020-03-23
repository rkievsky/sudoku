<?php

namespace Requests;

use Exceptions\RequestError;

class ConnectRQ extends BasicRQ
{
    /** @inheritDoc */
    public function collect(string $raw = null): \stdClass
    {
        try {
            return parent::collect();
        } catch (\Throwable $error) {
            throw RequestError::create(RequestError::CANT_COLLECT_REQUEST_DATA, null, $error);
        }
    }
}