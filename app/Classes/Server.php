<?php

namespace Classes;

use Exceptions\ServerError;

class Server extends BasicApplication
{
    const WAIT_TIMEOUT = null;

    /** @var resource $socket  */
    private $socket = null;

    /** @var array $connects */
    private $connects = [];

    private function keepAlive()
    {
        return true;
    }

    /**
     * @throws \Exceptions\BasicError
     */
    private function runServer()
    {
        $this->socket = stream_socket_server("tcp://0.0.0.0:8000", $errCode, $errMsg);
        if (!$this->socket) {
            throw ServerError::create(ServerError::CANT_OPEN_SOCKET, null, new \Exception($errMsg, $errCode));
        }

        while ($this->keepAlive()) {
            //формируем массив прослушиваемых сокетов:
            $read = $this->connects;
            $read[] = $this->socket;
            $write = $except = null;

            //ожидаем сокеты доступные для чтения (без таймаута)
            if (!stream_select($read, $write, $except, self::WAIT_TIMEOUT)) {
                break;
            }

            //есть новое соединение
            if (in_array($this->socket, $read)) {
                //принимаем новое соединение
                $connect = stream_socket_accept($this->socket, -1);
                //добавляем его в список необходимых для обработки
                $this->connects[] = $connect;
                unset($read[array_search($this->socket, $read)]);
            }

            //обрабатываем все соединения
            foreach($read as $connect) {
                $headers = '';
                while ($buffer = rtrim(fgets($connect))) {
                    $headers .= $buffer;
                }
                fwrite($connect, "HTTP/1.1 200 OK\r\nContent-Type: text/html\r\nConnection: close\r\n\r\nПривет");
                fclose($connect);
                unset($this->connects[ array_search($connect, $this->connects) ]);
            }
        }

        fclose($this->socket);
    }

    /**
     * @inheritDoc
     */
    public function run(): void
    {
        try {
            $this->runServer();
        } catch (\Throwable $error) {
            echo $error;
        }
    }
}