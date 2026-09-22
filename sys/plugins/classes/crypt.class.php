<?php

/**
 * Хэширование паролей.
 *
 * Здесь были encrypt/decrypt на mcrypt — ими шифровался пароль, который
 * движок клал в COOKIE «запомнить меня». mcrypt удалён в PHP 7.2, шифрование
 * молча выродилось в двойной base64, а сама схема заменена на токены
 * (см. user_token), поэтому методы удалены вместе с вектором в sys/ini/iv.dat.
 */
abstract class crypt {

    /**
     * делаем хэш пароля с наложением соли (покажем большой куй всем сервисам с md5 базами)
     * @param string $pass Исходный пароль
     * @param string $salt Соль
     * @return string Хэш пароля
     */
    static function hash($pass, $salt) {
        return md5($salt . md5((string) $pass) . md5($salt) . $salt);
    }

}
