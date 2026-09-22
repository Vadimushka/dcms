<?php

define("DCMS_REQUIRE_PHP_VERSION", "8.1");
/**
 * @const TIME_START Время запуска скрипта в миллисекундах
 */
define('TIME_START', microtime(true)); // время запуска скрипта

/**
 * @const DCMS Метка о DCMS
 */
define('DCMS', true);

/**
 * @const AJAX скрипт вызван AJAX запросом
 */
define('AJAX', strtolower(isset($_SERVER['HTTP_X_REQUESTED_WITH']) ? $_SERVER['HTTP_X_REQUESTED_WITH'] : '') == 'xmlhttprequest');

/**
 * @const IS_WINDOWS Запущено ли на винде
 */
if (!defined('IS_WINDOWS')) {
    define('IS_WINDOWS', strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');
}

// устанавливаем Московскую временную зону по умолчанию
if (@function_exists('ini_set')) {
    ini_set('date.timezone', 'Europe/Moscow');
}

/**
 * @const H путь к корневой директории сайта
 */
if (file_exists($_SERVER ['DOCUMENT_ROOT'] . '/sys/plugins/classes/dcms.class.php')) {
    define('H', $_SERVER ['DOCUMENT_ROOT']); // корневая директория сайта
} else {
    /* Если $_SERVER ['DOCUMENT_ROOT'] не является корневой директорией сайта, то будем искать ее вручную */

    $rel_path = '';
    $searched_file = 'sys/plugins/classes/dcms.class.php';
    for ($i = 0; $i < 10; $i++) {
        if (file_exists($rel_path . $searched_file)) {
            $abs_path = realpath($rel_path . $searched_file);
            break;
        }
        $rel_path .= '../';
    }
    define('H', str_replace($searched_file, '', str_replace('\\', '/', $abs_path))); // корневая директория сайта
    unset($rel_path, $searched_file, $abs_path);
}

/**
 * @const TEMP временная папка
 */
define('TEMP', H . '/sys/tmp');
/**
 * @const TMP временная папка
 */
define('TMP', H . '/sys/tmp');
/**
 * @const FILES Путь к папке загруз-центра
 */
define('FILES', realpath(H . '/sys/files'));
/**
 * @const TIME UNIXTIMESTAMP
 */
define('TIME', time());
/**
 * @const DAY_TIME UNIXTIMESTAMP на начало текущих суток
 */
define('DAY_TIME', mktime(0, 0, 0));
/**
 * @const IS_MAIN true, если мы на главной странице
 */
define('IS_MAIN', $_SERVER['SCRIPT_NAME'] == '/index.php');
/**
 * @const SESSION_LIFE_TIME время жизни сессии, а также время последней активности пользователей, считающихся онлайн
 */
define('SESSION_LIFE_TIME', 600);
/**
 * @const SESSION_NAME имя сессии
 */
define('SESSION_NAME', 'DCMS_SESSION');
/**
 * @const SESSION_ID_USER ключ сессий, в котором хранится идентификатор пользователя
 */
define('SESSION_ID_USER', 'DCMS_SESSION_ID_USER');
/**
 * Работает ли запрос по HTTPS. За обратным прокси (а так стоит боевой сайт)
 * $_SERVER['HTTPS'] пуст, и признак приходит заголовком либо виден по порту.
 * @return bool
 */
function is_https() {
    if (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off')
        return true;
    if (!empty($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443)
        return true;

    // За обратным прокси (так стоит боевой сайт) единственный признак TLS —
    // заголовок от прокси. Отличить его от клиентского по REMOTE_ADDR нельзя:
    // прокси подставляет туда реальный адрес посетителя, и движок сам берёт
    // адрес оттуда (browser.class.php, баны, журналы). Поэтому заголовку
    // доверяем, а перетирать его обязан прокси — см. docs/deploy/README.md.
    if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https')
        return true;
    return !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && strtolower($_SERVER['HTTP_X_FORWARDED_SSL']) === 'on';
}

/**
 * @const COOKIE_ID_USER идентификатор пользователя в COOKIE
 */
define('COOKIE_ID_USER', 'DCMS_COOKIE_ID_USER');
/**
 * @const COOKIE_USER_TOKEN токен «запомнить меня» в COOKIE (см. user_token)
 */
define('COOKIE_USER_TOKEN', 'DCMS_COOKIE_USER_TOKEN');
/**
 * @const COOKIE_USER_PASSWORD пароль пользователя в COOKIE. Схема заменена
 * на токены; константа осталась, чтобы стирать куки, выданные до перехода
 */
define('COOKIE_USER_PASSWORD', 'DCMS_COOKIE_USER_PASSWORD');
/**
 * @const SPRITE_CLASS_PREFIX префикс для css класса со спрайтами иконок
 */
define('SPRITE_CLASS_PREFIX', 'DCMS_');


if (@function_exists('ini_set')) {
    // время жизни сессии
    ini_set('session.cache_expire', SESSION_LIFE_TIME);

    // игнорировать повторяющиеся ошибки
    ini_set('ignore_repeated_errors', true);

    // Раньше здесь принудительно сужался error_reporting до E_ERROR и
    // включался display_errors — это перекрывало php.ini на каждом
    // запросе. На PHP 8 из-за этого E_COMPILE_ERROR (несовместимые
    // сигнатуры методов и т.п.) переставал не только показываться, но и
    // логироваться: ошибка вообще переставала быть видна. display_errors
    // и error_reporting теперь задаются один раз в php.ini контейнера
    // (Off / E_ALL) и здесь не перекрываются.
}

/**
 * @const URL текущая страница.
 */
define('URL', urlencode($_SERVER ['REQUEST_URI']));

if (function_exists('mb_internal_encoding')) {
    // Выставляем кодировку для mb_string  
    mb_internal_encoding('UTF-8');
}

/**
 * автоматическая загрузка классов
 * @param string $class_name имя класса
 * @deprecated
 */
function dcmsAutoload($class_name) {
    $path = H . '/sys/plugins/classes/' . strtolower($class_name) . '.class.php';
    if (file_exists($path)) {
        include_once ($path);
    }
}

spl_autoload_register('dcmsAutoload');

require_once(H . '/sys/plugins/classes/SplClassLoader.php');
$loader = new SplClassLoader(null, H . '/sys/plugins/classes/');
$loader->register();
unset($loader);

include_once (H . '/sys/plugins/classes/cache.class.php');

/**
 * Генератор пароля
 * @param int $len Длина пароля
 * @return string
 */
function passgen($len = 32) {
    $password = '';
    $small = 'abcdefghijklmnopqrstuvwxyz';
    $large = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $numbers = '1234567890';
    for ($i = 0; $i < $len; $i++) {
        switch (mt_rand(1, 3)) {
            case 3 :
                $password .= $large [mt_rand(0, 25)];
                break;
            case 2 :
                $password .= $small [mt_rand(0, 25)];
                break;
            case 1 :
                $password .= $numbers [mt_rand(0, 9)];
                break;
        }
    }
    return $password;
}

/**
 * @global \language_pack $user_language_pack Текущий языковой пакет
 */
$user_language_pack = new language_pack(false);

/**
 * Локализация текстовой строки.
 * ВНИМАНИЕ!!! не использовать динамические строки
 * @global language_pack $user_language_pack
 * @return string Локализованная строка
 */
function __() {
    $args = func_get_args();
    $args_num = count($args);
    if (!$args_num) {
        // нет ни строки ни параметров, вообще нихрена
        return '';
    }

    global $user_language_pack;
    $string = $user_language_pack->getString($args[0]);

    if ($args_num == 1) {
        // строка без параметров
        return $string;
    }

// строка с параметрами
    $args4eval = array();
    for ($i = 1; $i < $args_num; $i++) {
        $args4eval[] = '$args[' . $i . ']';
    }
    // vsprintf вместо eval: то же самое, но без выполнения кода на лету.
    // Битый формат (одиночный %% в строке или в переводе) с PHP 8 бросает ValueError
    // вместо предупреждения — одна опечатка в языковом файле не должна ронять страницу.
    $params = array_slice($args, 1);
    try {
        return vsprintf($string, $params);
    } catch (Exception $e) {
        return $string;
    } catch (Throwable $e) {
        return $string;
    }
}

// Сессионная кука уходила без единого атрибута: её не задавал ни код, ни
// прежняя конфигурация Apache. Задаём до старта сессии — иначе не применится.
// use_trans_sid оставлен включённым (движок рассчитывает на браузеры без кук),
// поэтому httponly защищает только саму куку, но это лучше, чем ничего.
session_set_cookie_params(array(
    'lifetime' => 0,
    'path' => '/',
    'httponly' => true,
    'samesite' => 'Lax',
    'secure' => is_https(),
));

@session_name(SESSION_NAME) or die(__('Невозможно инициализировать сессии'));
@session_start() or die(__('Невозможно инициализировать сессии'));
/**
 * @const SESS Идентификатор сессии
 */
define('SESS', preg_replace('#[^a-z0-9]#i', '', session_id()));