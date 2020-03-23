<?php

namespace Classes;

use Actions\Connect;
use Actions\ConnectToGame;
use Exceptions\ActionError;
use Exceptions\ServerError;
use Requests\BasicRQ;

class Server
{
    const WAIT_TIMEOUT = null;
    const MASTER_PORT = 8000;
    const INTERFACE_IP = '127.0.0.1';
    const HOST_NAME = 'sudoku.local';

    /**
     * @var Application $app
     */
    private $app = null;

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
        $this->socket = stream_socket_server(sprintf('tcp://%s:%d', self::INTERFACE_IP, self::MASTER_PORT), $errCode, $errMsg);
        if (!$this->socket) {
            throw ServerError::create(ServerError::CANT_OPEN_SOCKET, null, new \Exception($errMsg, $errCode));
        }

        $this->connects = array();
        while ($this->keepAlive()) {
            //формируем массив прослушиваемых сокетов:
            $read = $this->connects;
            $read []= $this->socket;
            $write = $except = null;

            //ожидаем сокеты доступные для чтения (без таймаута)
            if (!stream_select($read, $write, $except, null)) {
                break;
            }

            //есть новое соединение
            if (in_array($this->socket, $read)) {
                //принимаем новое соединение и производим рукопожатие:
                if (($connect = stream_socket_accept($this->socket, -1)) && $info = $this->handshake($connect)) {
                    //добавляем его в список необходимых для обработки
                    $this->connects[] = $connect;
                    //вызываем пользовательский сценарий
                    $this->onOpen($connect, $info);
                }
                unset($read[ array_search($this->socket, $read) ]);
            }

            //обрабатываем все соединения
            foreach($read as $connect) {
                $data = fread($connect, 100000);

                //соединение было закрыто
                if (!$data) {
                    fclose($connect);
                    unset($this->connects[ array_search($connect, $this->connects) ]);
                    //вызываем пользовательский сценарий
                    $this->onClose($connect);
                    continue;
                }

                //вызываем пользовательский сценарий
                $this->onMessage($connect, $data);
            }
        }

        fclose($this->socket);
    }

    private function handshake($connect) {
        $info = [];

        $line = fgets($connect);
        $header = explode(' ', $line);
        $info['method'] = $header[0];
        $info['uri'] = $header[1];

        //считываем заголовки из соединения
        while ($line = rtrim(fgets($connect))) {
            if (preg_match('/\A(\S+): (.*)\z/', $line, $matches)) {
                $info[$matches[1]] = $matches[2];
            } else {
                break;
            }
        }

        //получаем адрес клиента
        $address = explode(':', stream_socket_get_name($connect, true));
        $info['ip'] = $address[0];
        $info['port'] = $address[1];

        if (empty($info['Sec-WebSocket-Key'])) {
            return false;
        }

        //отправляем заголовок согласно протоколу вебсокета
        $SecWebSocketAccept = base64_encode(pack('H*', sha1($info['Sec-WebSocket-Key'] . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11')));
        $upgrade = "HTTP/1.1 101 Web Socket Protocol Handshake\r\n" .
            "Upgrade: websocket\r\n" .
            "Connection: Upgrade\r\n" .
            "Sec-WebSocket-Accept:$SecWebSocketAccept\r\n\r\n";
        fwrite($connect, $upgrade);

        return $info;
    }

    private function encode($payload, $type = 'text', $masked = false)
    {
        $frameHead = [];
        $payloadLength = strlen($payload);

        switch ($type) {
            case 'text':
                // first byte indicates FIN, Text-Frame (10000001):
                $frameHead[0] = 129;
                break;

            case 'close':
                // first byte indicates FIN, Close Frame(10001000):
                $frameHead[0] = 136;
                break;

            case 'ping':
                // first byte indicates FIN, Ping frame (10001001):
                $frameHead[0] = 137;
                break;

            case 'pong':
                // first byte indicates FIN, Pong frame (10001010):
                $frameHead[0] = 138;
                break;
        }

        // set mask and payload length (using 1, 3 or 9 bytes)
        if ($payloadLength > 65535) {
            $payloadLengthBin = str_split(sprintf('%064b', $payloadLength), 8);
            $frameHead[1] = ($masked === true) ? 255 : 127;
            for ($i = 0; $i < 8; $i++) {
                $frameHead[$i + 2] = bindec($payloadLengthBin[$i]);
            }
            // most significant bit MUST be 0
            if ($frameHead[2] > 127) {
                return array('type' => '', 'payload' => '', 'error' => 'frame too large (1004)');
            }
        } elseif ($payloadLength > 125) {
            $payloadLengthBin = str_split(sprintf('%016b', $payloadLength), 8);
            $frameHead[1] = ($masked === true) ? 254 : 126;
            $frameHead[2] = bindec($payloadLengthBin[0]);
            $frameHead[3] = bindec($payloadLengthBin[1]);
        } else {
            $frameHead[1] = ($masked === true) ? $payloadLength + 128 : $payloadLength;
        }

        // convert frame-head to string:
        foreach (array_keys($frameHead) as $i) {
            $frameHead[$i] = chr($frameHead[$i]);
        }
        if ($masked === true) {
            // generate a random mask:
            $mask = array();
            for ($i = 0; $i < 4; $i++) {
                $mask[$i] = chr(rand(0, 255));
            }

            $frameHead = array_merge($frameHead, $mask);
        }
        $frame = implode('', $frameHead);

        // append payload to frame:
        for ($i = 0; $i < $payloadLength; $i++) {
            $frame .= ($masked === true) ? $payload[$i] ^ $mask[$i % 4] : $payload[$i];
        }

        return $frame;
    }

    private function decode($data)
    {
        $unmaskedPayload = '';
        $decodedData = [];

        // estimate frame type:
        $firstByteBinary = sprintf('%08b', ord($data[0]));
        $secondByteBinary = sprintf('%08b', ord($data[1]));
        $opcode = bindec(substr($firstByteBinary, 4, 4));
        $isMasked = ($secondByteBinary[0] == '1') ? true : false;
        $payloadLength = ord($data[1]) & 127;

        // unmasked frame is received:
        if (!$isMasked) {
            return array('type' => '', 'payload' => '', 'error' => 'protocol error (1002)');
        }

        switch ($opcode) {
            // text frame:
            case 1:
                $decodedData['type'] = 'text';
                break;

            case 2:
                $decodedData['type'] = 'binary';
                break;

            // connection close frame:
            case 8:
                $decodedData['type'] = 'close';
                break;

            // ping frame:
            case 9:
                $decodedData['type'] = 'ping';
                break;

            // pong frame:
            case 10:
                $decodedData['type'] = 'pong';
                break;

            default:
                return array('type' => '', 'payload' => '', 'error' => 'unknown opcode (1003)');
        }

        if ($payloadLength === 126) {
            $mask = substr($data, 4, 4);
            $payloadOffset = 8;
            $dataLength = bindec(sprintf('%08b', ord($data[2])) . sprintf('%08b', ord($data[3]))) + $payloadOffset;
        } elseif ($payloadLength === 127) {
            $mask = substr($data, 10, 4);
            $payloadOffset = 14;
            $tmp = '';
            for ($i = 0; $i < 8; $i++) {
                $tmp .= sprintf('%08b', ord($data[$i + 2]));
            }
            $dataLength = bindec($tmp) + $payloadOffset;
            unset($tmp);
        } else {
            $mask = substr($data, 2, 4);
            $payloadOffset = 6;
            $dataLength = $payloadLength + $payloadOffset;
        }

        /**
         * We have to check for large frames here. socket_recv cuts at 1024 bytes
         * so if websocket-frame is > 1024 bytes we have to wait until whole
         * data is transferd.
         */
        if (strlen($data) < $dataLength) {
            return false;
        }

        if ($isMasked) {
            for ($i = $payloadOffset; $i < $dataLength; $i++) {
                $j = $i - $payloadOffset;
                if (isset($data[$i])) {
                    $unmaskedPayload .= $data[$i] ^ $mask[$j % 4];
                }
            }
            $decodedData['payload'] = $unmaskedPayload;
        } else {
            $payloadOffset = $payloadOffset - 4;
            $decodedData['payload'] = substr($data, $payloadOffset);
        }

        return $decodedData;
    }

    private function processRQ($connect, string $action, string $request = null): void
    {
        try {
            $response = $this->app->processRQ($action, $request);
        } catch (\Throwable $error) {
            $response = null;
            $this->app->processError($error, $response);
        } finally {
            fwrite($connect, $this->encode(json_encode($response)));
        }
    }

    /**
     * @param string $request
     * @return string
     * @throws \Exceptions\BasicError
     */
    private function getAction(string $request): string
    {
        try {
            /** @var BasicRQ $rq */
            $rq = BasicRQ::create($request);
            return $rq->action;
        } catch (\Throwable $error) {
            throw ActionError::create(ActionError::UNKNOWN_ACTION);
        }
    }

    /**
     * @param $connect
     * @param $rq
     *
     * @throws \Exceptions\BasicError
     */
    private function onOpen($connect, $rq)
    {
        $this->processRQ($connect, (new Connect())->getPrimitive());
    }

    private function onClose($connect)
    {
        //todo отписать слушателя
    }

    /**
     * @param $connect
     * @param $data
     * @throws \Exceptions\BasicError
     */
    private function onMessage($connect, $data)
    {
        $request = $this->decode($data);
        if ($request['type'] === 'close') {
            //todo отписать слушателя
            return;
        }

        $this->processRQ($connect, $this->getAction($request['payload']), $request['payload']);
    }

    public function __construct()
    {
        $this->app = Application::create();
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