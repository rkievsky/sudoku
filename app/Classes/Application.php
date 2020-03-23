<?php

namespace Classes;

use Actions\ActionsFactory;
use Classes\Game\Game;
use Exceptions\ActionError;
use Exceptions\RequestError;
use Responses\BasicRS;
use Responses\ErrorRS;

/**
 * Class Application
 *
 * Обеспечивает работу приложения
 */
class Application
{
    /** @var bool $isWeb */
    private $isWeb = false;

    /** @var ActionsFactory $actionsFactory */
    private $actionsFactory = null;

    /** @var Game $game  */
    private $game = null;

    /** @var static */
    private static $app;

    /**
     * Создаёт синглтон
     *
     * @return static
     */
    public static function create()
    {
        return self::$app = new static();
    }

    /**
     * Возвращает синглтон
     *
     * @return static
     */
    public static function get()
    {
        return self::$app;
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

        $response = new ErrorRS('', $this->game->getId());
        $response->success = false;
        $response->errorCode = $error->getCode();
        $response->errorMessage = $error->getMessage();

        return $httpCode;
    }

    /**
     * Application constructor.
     */
    public function __construct()
    {
        $this->actionsFactory = new ActionsFactory();
        if (!$this->isWeb()) {
            $this->game = new Game();
        }

    }

    /**
     * @param string|null $action
     * @param string|null $rawRQ
     * @return BasicRS
     * @throws \Exceptions\BasicError
     */
    public function processRQ(string $action = null, string $rawRQ = null): BasicRS
    {
        if (is_null($action)) {
            $action = trim($_SERVER['REQUEST_URI'], '/');
        }

        if (is_null($rawRQ)) {
            $rawRQ = file_get_contents('php://input');
        }

        $action = $this->actionsFactory->create($action);
        $request = $action->makeRQ($rawRQ);

        return $action->handle($request);
    }

    /** @inheritDoc */
    public function run(): void
    {
        $httpCode = 200;
        try {
            ob_start();

            $response = $this->processRQ();

            echo json_encode($response);
        } catch (\Throwable $error) {
            ob_clean();

            $response = null;
            $httpCode = $this->processError($error, $response);
            echo json_encode($response);
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

    /**
     * @return bool
     */
    public function isWeb(): bool
    {
        return $this->isWeb;
    }

    /**
     * @param bool $isWeb
     */
    public function setIsWeb(bool $isWeb): self
    {
        $this->isWeb = $isWeb;

        return $this;
    }
}