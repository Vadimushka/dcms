<?php

// конвертер кодировки
abstract class convert {

    // конвертируем из UTF в нужную кодировку (по-умолчанию Windows 1251)
    static function of_utf8($str, $to = 'cp1251') {
        if (self::charset($str) == 'UTF-8') {
            if (function_exists('mb_substr')) {
                return mb_convert_encoding($str, $to, 'UTF-8');
            }
            if (function_exists('iconv')) {
                return iconv('UTF-8', $to, $str);
            }
        }
        return $str;
    }

    // конвертируем в UTF из заданной кодировки (по-умолчанию Windows 1251)
    static function to_utf8($str, $from = 'cp1251') {
        if (self::charset($str) == $from) {
            if (function_exists('mb_substr')) {
                return mb_convert_encoding($str, 'UTF-8', $from);
            }
            if (function_exists('iconv')) {
                return iconv($from, 'UTF-8', $str);
            }
        }
        return $str;
    }

    static function charset($str) {
        if (preg_match('#[[:alpha:]]+#ui', $str)) {
            return 'UTF-8';
        } else {
            return 'cp1251';
        }
    }

}

?>