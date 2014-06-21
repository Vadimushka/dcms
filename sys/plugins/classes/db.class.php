<?php

/**
 * Class ExceptionPdoNotExists
 */
class ExceptionPdoNotExists extends Exception
{

}

/*
  Класс для подключения к БД
  Можно использовать в любом месте движка
  $db = DB::me();
 */

Class DB
{

    static protected $db;

    /**
     * @return DebugPDO
     * @throws ExceptionPdoNotExists
     * @throws Exception
     */
    static public function me()
    {
        if (!class_exists('pdo') || array_search('mysql', PDO::getAvailableDrivers()) === false)
            throw new ExceptionPdoNotExists("Отсутствует драйвер PDO");

        if (is_null(self::$db)) {
            $args = func_get_args();
            if (!isset($args[3])) {
                throw new Exception('Укажите параметры соединения');
            }
            self::$db = new DebugPDO('mysql:host=' . $args[0] . ';dbname=' . $args[1], $args[2], $args[3]);
        }
        return self::$db;
    }

    protected function __construct()
    {

    }

}