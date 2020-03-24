<?php

require "autoload.php";

use Classes\WebSocketServer;

(new WebSocketServer())->run();