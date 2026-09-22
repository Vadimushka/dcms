<?php

/**
 * Токены «запомнить меня».
 *
 * Раньше движок клал в COOKIE сам пароль пользователя, зашифрованный mcrypt.
 * После удаления mcrypt (PHP 7.2) шифрование молча выродилось в двойной
 * base64, то есть пароль лежал в куке открытым текстом. Теперь в куке —
 * случайный токен, а в базе хранится только его sha256: содержимое куки
 * не позволяет узнать пароль, а содержимое таблицы — войти на сайт.
 */
abstract class user_token {

    /** Срок жизни токена по умолчанию, секунд (год) */
    const TTL = 31536000;

    /** Длина токена в байтах до hex-кодирования */
    const LENGTH = 32;

    /** Сколько запомненных устройств держим на пользователя */
    const LIMIT_PER_USER = 20;

    /**
     * Создание токена для пользователя
     * @param int $id_user
     * @param int $ttl Время жизни в секундах
     * @return string|false Токен для записи в COOKIE (hex, 64 символа)
     */
    public static function create($id_user, $ttl = self::TTL) {
        try {
            $token = bin2hex(random_bytes(self::LENGTH));

            $res = DB::me()->prepare("INSERT INTO `users_tokens` (`id_user`, `token_hash`, `time_create`, `time_expire`) VALUES (?, ?, ?, ?)");
            $res->execute(array($id_user, self::hash($token), TIME, TIME + $ttl));
        } catch (Throwable $e) {
            // сюда попадает и отказ источника случайности (Random\RandomException):
            // предсказуемый токен хуже отсутствующего
            self::report($e);
            return false;
        }

        self::clean();
        self::trim($id_user);

        return $token;
    }

    /**
     * Проверка токена из COOKIE
     * @param string $token
     * @return int|false Идентификатор пользователя или false
     */
    public static function check($token) {
        if (!is_string($token) || $token === '')
            return false;

        try {
            $res = DB::me()->prepare("SELECT `id_user` FROM `users_tokens` WHERE `token_hash` = ? AND `time_expire` > ? LIMIT 1");
            $res->execute(array(self::hash($token), TIME));
        } catch (PDOException $e) {
            // без таблицы вход по токену невозможен — это отказ, а не пропуск
            self::report($e);
            return false;
        }

        $id_user = (int) $res->fetchColumn();

        // ноль — это не «нет пользователя», а системный бот с шестой группой:
        // токен с таким владельцем пустил бы в сайт от его имени
        return $id_user > 0 ? $id_user : false;
    }

    /**
     * Удаление одного токена (выход на этом устройстве)
     * @param string $token
     */
    public static function delete($token) {
        if (!is_string($token) || $token === '')
            return;

        try {
            $res = DB::me()->prepare("DELETE FROM `users_tokens` WHERE `token_hash` = ? LIMIT 1");
            $res->execute(array(self::hash($token)));
        } catch (PDOException $e) {
            self::report($e);
        }
    }

    /**
     * Удаление всех токенов пользователя: смена пароля разлогинивает
     * все запомненные устройства, включая угнанные
     * @param int $id_user
     * @return bool Удалось ли отозвать
     */
    public static function deleteAll($id_user) {
        try {
            $res = DB::me()->prepare("DELETE FROM `users_tokens` WHERE `id_user` = ?");
            $res->execute(array($id_user));
        } catch (PDOException $e) {
            self::report($e);
            return false;
        }
        return true;
    }

    /**
     * Ограничение числа живых токенов на пользователя: срок жизни — год, и
     * без потолка таблица растёт от каждого входа с нового устройства.
     * Лишними считаются самые старые.
     * @param int $id_user
     */
    protected static function trim($id_user) {
        try {
            // подзапрос обёрнут в производную таблицу: MySQL не разрешает
            // DELETE с выборкой из той же таблицы напрямую
            $res = DB::me()->prepare("DELETE FROM `users_tokens` WHERE `id_user` = ? AND `id` NOT IN "
                    . "(SELECT `id` FROM (SELECT `id` FROM `users_tokens` WHERE `id_user` = ? ORDER BY `time_create` DESC LIMIT " . (int) self::LIMIT_PER_USER . ") AS keep_list)");
            $res->execute(array($id_user, $id_user));
        } catch (PDOException $e) {
            self::report($e);
        }
    }

    /**
     * Удаление просроченных токенов. Вызывается при создании нового —
     * отдельная задача в cron ради этого не нужна.
     */
    public static function clean() {
        try {
            $res = DB::me()->prepare("DELETE FROM `users_tokens` WHERE `time_expire` < ?");
            $res->execute(array(TIME));
        } catch (PDOException $e) {
            self::report($e);
        }
    }

    /**
     * Запись токена в COOKIE. Параметры заданы здесь, а не в местах вызова,
     * чтобы httponly и samesite нельзя было забыть в одном из них.
     * @param string $token
     * @param int $ttl
     */
    public static function setCookie($token, $ttl = self::TTL) {
        setcookie(COOKIE_USER_TOKEN, $token, array(
            'expires' => TIME + $ttl,
            'path' => '/',
            'httponly' => true,
            'samesite' => 'Lax',
            'secure' => is_https(),
        ));
    }

    /**
     * Стирание COOKIE токена. Заодно стирается устаревшая COOKIE с паролем:
     * у пользователей, запомнившихся до перехода на токены, она ещё лежит
     * в браузере, и оставлять её там незачем.
     */
    public static function clearCookie() {
        setcookie(COOKIE_USER_TOKEN, '', array('expires' => TIME - 3600, 'path' => '/'));
        setcookie(COOKIE_USER_PASSWORD, '', array('expires' => TIME - 3600, 'path' => '/'));
    }

    /**
     * Запись сбоя в лог. Таблица users_tokens создаётся отдельным шагом
     * выката (см. docs/deploy/README.md), и до её появления «запомнить меня»
     * не должно ронять ни вход, ни смену пароля: без токена пользователь
     * просто входит заново. Ошибка не проглатывается — она видна в логе.
     * @param Throwable $e
     */
    protected static function report(Throwable $e) {
        error_log('user_token: ' . $e->getMessage() . ' (нужна таблица users_tokens, см. sys/preinstall)');
    }

    /**
     * Хэш токена для хранения в базе. Токен — случайные 256 бит, перебор
     * невозможен, поэтому соль и медленный алгоритм здесь не нужны.
     * @param string $token
     * @return string
     */
    protected static function hash($token) {
        return hash('sha256', $token);
    }

}
