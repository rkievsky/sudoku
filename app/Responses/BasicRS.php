<?php

namespace Responses;

abstract class BasicRS
{
    /** @var string $gameId */
    public $gameId = null;

    /** @var string $action */
    public $action = null;

    /** @var bool $success Признак успешного завершения запроса */
    public $success = true;

    /** @var int $errorCode Код ошибки */
    public $errorCode = null;

    /** @var string $errorMessage Сообщение об ошибке  */
    public $errorMessage = null;

    public function __construct(string $action, string $gameId)
    {
        $this->action = $action;
        $this->gameId = $gameId;
    }
}