<?php

namespace Classes;

class Listener
{
    /** @var resource $socket */
    private $socket;

    /**
     * Listener constructor.
     *
     * @param resource $socket
     */
    public function __construct($socket)
    {
        $this->socket = $socket;
    }
}