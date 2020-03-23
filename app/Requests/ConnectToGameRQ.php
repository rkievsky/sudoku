<?php

namespace Requests;

use Exceptions\RequestError;

class ConnectToGameRQ extends BasicRQ
{
    /** @var string $name  */
    public $name = null;

    public function collect(): void
    {
        try {
            $body = json_decode(file_get_contents('php://input'));
            $this->name = $body->name;
        } catch (\Throwable $error) {
            throw RequestError::create(RequestError::CANT_COLLECT_REQUEST_DATA, null, $error);
        }
    }
}