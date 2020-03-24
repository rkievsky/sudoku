<?php

/**
 * Функция загрузчик классов. Ищет классы в папках, исходя из их namespace
 *
 * @param string      $class
 * @param string|null $file_extensions
 */
spl_autoload_register(function (string $class, string $file_extensions = null) {
    if ($file_extensions === null) {
        $file_extensions = spl_autoload_extensions();
    }

    $path = explode('\\', $class);
    if (count($path) > 1) {
        $className = array_slice($path, count($path) - 1, 1)[0];
        $path = implode(DIRECTORY_SEPARATOR, array_slice($path, 0, count($path) - 1));
    } else {
        $path = '.';
        $className = $class;
    }

    foreach (explode(',', $file_extensions) as $fileExt) {
        $fileName = $path . DIRECTORY_SEPARATOR . $className . $fileExt;

        if (file_exists($fileName)) {
            require $fileName;
            break;
        }
    }
});

/**
 * Returns a GUIDv4 string
 *
 * Uses the best cryptographically secure method
 * for all supported pltforms with fallback to an older,
 * less secure version.
 *
 * @see http://php.net/manual/ru/function.com-create-guid.php
 *
 * @param bool $trim
 * @return string
 */
function getGUID()
{
    // Windows
    if (function_exists('com_create_guid') === true) {
        return str_replace(['{', '}', '-'], ['', '', ''], com_create_guid());
    }

    // OSX/Linux
    if (function_exists('openssl_random_pseudo_bytes') === true) {
        $data = openssl_random_pseudo_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);    // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);    // set bits 6-7 to 10

        return bin2hex($data);
    }

    // Fallback (PHP 4.2+)
    mt_srand((double) microtime() * 10000);
    $charid = strtolower(md5(uniqid(rand(), true)));
    $guidv4 =
        substr($charid, 0, 8) .
        substr($charid, 8, 4) .
        substr($charid, 12, 4) .
        substr($charid, 16, 4) .
        substr($charid, 20, 12);

    return $guidv4;
}
