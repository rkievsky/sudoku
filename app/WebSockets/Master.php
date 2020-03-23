<?php

namespace WebSockets;

class Master
{
    protected $workers = array();
    protected $clients = array();

    public function __construct($workers) {
        $this->clients = $this->workers = $workers;
    }

    public function start() {
        while (true) {
            //подготавливаем массив всех сокетов, которые нужно обработать
            $read = $this->clients;

            //обновляем массив сокетов, которые можно обработать
            stream_select($read, $write, $except, null);

            if (!$read) {
                continue;
            }

            //пришли данные от подключенных клиентов
            foreach ($read as $client) {
                $data = fread($client, 1000);

                if (!$data) {
                    //соединение было закрыто
                    unset($this->clients[intval($client)]);
                    fclose($client);
                    continue;
                }

                foreach ($this->workers as $worker) {
                    //пересылаем данные во все воркеры
                    if ($worker !== $client) {
                        fwrite($worker, $data);
                    }
                }
            }
        }
    }
}
