<?php

namespace Classes;

use Actions\ActionsFactory;
use Classes\Game\Players;
use Classes\Game\Sudoku;
use Exceptions\ActionError;
use Exceptions\RequestError;
use Responses\BasicRS;
use Responses\ErrorRS;

class Server
{
    const WAIT_TIMEOUT = null;
    const MASTER_PORT = 8000;
    const INTERFACE_IP = '127.0.0.1';
    const HOST_NAME = 'sudoku.local';

    /** @var ActionsFactory $actionsFactory */
    private $actionsFactory = null;

    /** @var bool $needToNotifyListeners */
    private $needToNotifyListeners = false;

    /** @var string $gameId  */
    private $gameId = null;

    /** @var Sudoku $sudoku */
    public $sudoku = null;

    /** @var Players  */
    public $players = null;

    /**
     * Application constructor.
     *
     * @throws \Exceptions\BasicError
     */
    public function __construct()
    {
        $this->actionsFactory = new ActionsFactory();

        $this->gameId = getGUID();
        $this->sudoku = new Sudoku($this->gameId);
        $this->players = new Players($this->gameId);
    }

    /**
     * @param string      $type
     * @param string|null $body
     * @return BasicRS
     * @throws \Exceptions\BasicError
     */
    public function processRQ(string $type, string $body = null): BasicRS
    {
        if ($type == 'close') {
            // todo надо что-то предпринять
        }

        $json = $body ? json_decode($body) : null;
        $action = $this->actionsFactory->create($json->action ?? null, $this);
        $request = $action->makeRQ($json);

        return $action->handle($request);
    }

    /**
     * Отправляет ответ с информацией об ошибке
     *
     * @param \Throwable $error
     * @param $response
     * @return int|string
     */
    public function processError(\Throwable $error, &$response)
    {
        $httpCode = 500;
        switch (get_class($error)) {
            case ActionError::class:
                $httpCode = 404;
                break;
            case RequestError::class:
                $httpCode = 400;
                break;
        }

        $response = new ErrorRS('error', $this->getGameId());
        $response->success = false;
        $response->errorCode = $error->getCode();
        $response->errorMessage = $error->getMessage();

        return $httpCode;
    }

    public function setNeedToNotifyListeners($need = true)
    {
        $this->needToNotifyListeners = $need;
    }

    public function needToNotifyListeners()
    {
        $current = $this->needToNotifyListeners;
        $this->needToNotifyListeners = false;

        return $current;
    }

    public function getGameId(): string
    {
        return $this->gameId;
    }
}