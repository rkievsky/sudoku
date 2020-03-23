<?php

namespace Classes;

use Actions\ActionsFactory;
use Classes\Game\Game;
use Exceptions\ActionError;
use Exceptions\RequestError;
use Responses\ErrorRS;

/**
 * Class Application
 *
 * Обеспечивает работу приложения
 */
class Application extends BasicApplication
{
    /** @var ActionsFactory $actionsFactory */
    private $actionsFactory = null;

    /** @var \Actions\IAction $action */
    private $action = null;

    /** @var \Requests\BasicRQ $request */
    private $request = null;

    /** @var Game $game  */
    private $game = null;

    /**
     * Отправляет ответ с информацией об ошибке
     *
     * @param \Throwable $error
     * @return int
     */
    private function processError(\Throwable $error): int
    {
        $httpCode = 500;
        switch (get_class($error)) {
            case ActionError::class:
                $httpCode = 404;
                break;
            case RequestError::class:
                $httpCode = 400;
        }

        $response = new ErrorRS();
        $response->success = false;
        $response->errorCode = $error->getCode();
        $response->errorMessage = $error->getMessage();

        echo json_encode($response);

        return $httpCode;
    }

    /**
     * Application constructor.
     */
    public function __construct()
    {
        $this->actionsFactory = new ActionsFactory();
    }

    /** @inheritDoc */
    public function run(): void
    {
        $httpCode = 200;
        try {
            ob_start();

            $this->action = $this->actionsFactory->create();
            $this->request = $this->action->makeRQ();

            if ($this->request->gameId) {
                $this->game = new Game($this->request->gameId);
            } else {
                $this->game = new Game();
            }

            $response = $this->action->handle($this->request);
            echo json_encode($response);
        } catch (\Throwable $error) {
            ob_clean();

            $httpCode = $this->processError($error);
        } finally {
            ob_flush();
            http_response_code($httpCode);
        }
    }

    /**
     * Получает игру
     *
     * @param string $id идентификатор сессии игры
     *
     * @return Game
     */
    public function getGame($id = null): Game
    {
        return $this->game;
    }
}