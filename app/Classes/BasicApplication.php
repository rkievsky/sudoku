<?php

namespace Classes;

abstract class BasicApplication
{
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
     * Запуск приложения
     *
     * @return int
     */
    abstract public function run(): void;
}