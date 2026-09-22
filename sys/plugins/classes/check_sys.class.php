<?php

/**
 * Проверка системы при установке (и в админке)
 */
class check_sys {

    public $errors = array(); // ошибки, при которых система не может работать
    public $notices = array(); // ошибки, при которых система может работать нестабильно или могут не работать некоторые дополнительные возможности
    public $oks = array(); // отчет о работоспособности проверяемого модуля

    function __construct() {
        $this->_checkSys();
    }

    /**
     * Возвращает массив файлов с ошибками CHMOD
     * @param string $path путь относительно корня сайта
	 * @param string|bool $errorIfNotExists если путь не существует, то считать ошибкой
     * @return array
     */
    static public function getChmodErr($path, $errorIfNotExists = false) {
        $err = array();

        if (is_file(H . '/' . $path)) {
            if (!is_writable(H . '/' . $path))
                $err[] = __('Нет прав на запись: %s', $path);
        } elseif (is_dir(H . '/' . $path)) {
            $od = opendir(H . '/' . $path);
            while ($rd = readdir($od)) {
                if (stripos($rd, '.') !== false) {
                    continue;
                }
                $err = array_merge($err, self::getChmodErr($path . '/' . $rd, $errorIfNotExists));
            }
            closedir($od);
        } elseif ($errorIfNotExists) {
            $err[] = __('%s отсутствует', $path);
        }

        return $err;
    }

    protected function _checkSys() {
        // проверка версии PHP
        if (version_compare(PHP_VERSION, DCMS_REQUIRE_PHP_VERSION, '>=')) {
            $this->oks[] = 'PHP >= ' . DCMS_REQUIRE_PHP_VERSION . ': ОК (' . PHP_VERSION . ')';
        } else {
            $this->errors[] = __('Требуется PHP >= %s (сейчас %s)', DCMS_REQUIRE_PHP_VERSION, PHP_VERSION);
        }

        // проверка MySQL через PDO
        if (class_exists('PDO') && in_array('mysql', PDO::getAvailableDrivers(), true)) {
            $this->oks[] = 'MySQL (PDO): OK';
        } else {
            $this->errors[] = __('Нет драйвера mysql для PDO');
        }

        // работа с графикой
        if (function_exists('gd_info')) {
            $this->oks[] = 'GD: OK';
        } else {
            $this->errors[] = __('Нет библиотеки GD');
        }

        // снятие ограничения по времени выполнения скрипта
        if (function_exists('set_time_limit')) {
            $this->oks[] = 'set_time_limit: OK';
        } else {
            $this->notices[] = __('Функция set_time_limit() не доступна. Могут возникнуть проблемы при обработке ресурсоемких задач.');
        }  // функции для работы с UTF
        if (function_exists('mb_internal_encoding') && function_exists('iconv')) {
            $this->oks[] = 'mbstring и Iconv: OK';
        } elseif (!function_exists('mb_internal_encoding') && !function_exists('iconv')) {
            $this->errors[] = __('Необходим по крайней мере один из модулей: mbstring или Iconv');
        } elseif (function_exists('mb_internal_encoding')) {
            $this->oks[] = 'mbstring: OK';
        } elseif (function_exists('iconv')) {
            $this->oks[] = 'Iconv: OK';
        }
        // обработка видео (снятие скриншотов)
        if (files_screen_ff::isAvailable()) {
            $this->oks[] = 'FFmpeg: OK';
        } else {
            $this->notices[] = __('Без FFmpeg автоматическое создание скриншотов к видео недоступно');
        }



// передача сессии в URI
        if (ini_get('session.use_trans_sid')) {
            $this->notices[] = __('Параметр session.use_trans_sid включён: идентификатор сессии попадает в адрес страницы и утекает через Referer и историю браузера. В PHP 8.5 механизм объявлен устаревшим');
        } else {
            $this->oks[] = 'session.use_trans_sid: OK';
        }  // экранирование кавычек'
        if (ini_get('arg_separator.output') == '&amp;') {
            $this->oks[] = 'arg_separator.output: &amp;amp;: OK';
        } else {
            $this->notices[] = 'arg_separator.output: ' . text::toOutput(ini_get('arg_separator.output')) . ' ' . __('Возможно появление xml ошибок');
        }
    }

}

?>
