<?php

require "autoload.php";

use Classes\Application;

Application::create()->setIsWeb(true)->run();
