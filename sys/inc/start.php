<?php
// Проверяем версию PHP
version_compare(PHP_VERSION, '5.2', '>=') or die('Требуется PHP >= 5.2');

define('TIME_START', microtime(true)); // время запуска скрипта
define('DCMS', true);

// проверка на AJAX запрос
define('AJAX', strtolower(@$_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');

// проверка операционной системы
if (!defined('IS_WINDOWS')) {
    define('IS_WINDOWS', strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');
}

// устанавливаем Московскую временную зону по умолчанию
if (@function_exists('ini_set')) {
    ini_set('date.timezone', 'Europe/Moscow');
}

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

// Временные папки
define('TEMP', H . '/sys/tmp');
define('TMP', H . '/sys/tmp');

// папка со всеми пользовательскими файлами
define('FILES', realpath(H . '/sys/files'));

// временная метка unix
define('TIME', time());

// временная метка на начало текущих суток
define('DAY_TIME', mktime(0, 0, 0));

// главная страница
define('IS_MAIN', $_SERVER['SCRIPT_NAME'] == '/index.php');

// время жизни сессии, а также время последней активности пользователей, считающихся онлайн
define('SESSION_LIFE_TIME', 600);

// имя сессии
define('SESSION_NAME', 'DCMS_SESSION');

// ключ сессий, в котором хранится идентификатор пользователя
define('SESSION_ID_USER', 'DCMS_SESSION_ID_USER');

// ключ сессий, в котором хранится пароль пользователя
define('SESSION_PASSWORD_USER', 'DCMS_SESSION_PASSWORD_USER');

// идентификатор пользователя в COOKIE
define('COOKIE_ID_USER', 'DCMS_COOKIE_ID_USER');

// пароль пользователя в COOKIE
define('COOKIE_USER_PASSWORD', 'DCMS_COOKIE_USER_PASSWORD');


if (@function_exists('ini_set')) {

    // время жизни сессии
    ini_set('session.cache_expire', SESSION_LIFE_TIME);

    // игнорировать повторяющиеся ошибки
    ini_set('ignore_repeated_errors', true);

    // показываем только фатальные ошибки
    ini_set('error_reporting', E_ERROR);

    // непосредственно, включаем показ ошибок
    ini_set('display_errors', true);
}

// текущая страница
define('URL', urlencode($_SERVER ['REQUEST_URI']));

if (function_exists('mb_internal_encoding')) {
    // Выставляем кодировку для mb_string  
    mb_internal_encoding('UTF-8');
}

if (function_exists('iconv')) {
    // Выставляем кодировку для Iconv
    iconv_set_encoding('internal_encoding', 'UTF-8');
}

// функция автоматической загрузки классов
function dcmsAutoload($class_name) {
    $path = H . '/sys/plugins/classes/' . strtolower($class_name) . '.class.php';
    if (file_exists($path)) {
        include_once ($path);
    }
}
// регистрируем функцию для автоматической загрузки классов
spl_autoload_register('dcmsAutoload');

debug::step('Запуск');
include_once (H . '/sys/plugins/classes/cache.class.php');
debug::step('Подключение кэша');

// во время автоматического обновления не должно быть запросов со стороны пользователя
if (cache_events::get('system.update.work')) {
    exit('Выполняется обновление системы. Пожалуйста, обновите страницу позже.');
}

// генератор пароля
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

/* Переназначение функций */

/**
 * Псевдоним mysql_real_escape_string
 * @param string $str
 * @return string
 */
function my_esc($str) {
    return mysql_real_escape_string($str);
}

/**
 *  фильтрование текста для полей ввода
 * @param string $text
 * @return string
 */
function for_value($text) {
    return text::for_value($text);
}

/**
 * Фильтрование и форматирование текста с обработкой BBCODE
 * @param string $text
 * @return string
 */
function output_text($text) {
    return text::output_text($text);
}

/**
 * Возвращает название месяца
 * @param integer $mes номер месяца
 * @param boolean $v Использование родительного падежа
 * @return string
 */
function rus_mes($mes, $v = 1) {
    return misc::rus_mes($mes, $v);
}

/**
 * Возвращает читабельное представление времени
 * @param integer $t UNIXTIMESTAMP
 * @return string
 */
function vremja($t) {
    return misc::vremja($t);
}

/**
 * Возвращает читабельное представление объема данных
 * @param integer $filesize Объем данных в байтах
 * @return string
 */
function size_data($filesize = 0) {
    return misc::size_data($filesize);
}

debug::step('Определение функций');


$user_language_pack = new language_pack(false);
debug::step('Подключение языкового пакета по-умолчанию');

/**
 * Локализация текстовой строки
 * @global language_pack $user_language_pack
 * @return string
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
    return eval('return sprintf($string,' . implode(',', $args4eval) . ');');
}

/* ---------------------- */

/* Инициализация механизма сессий  */
session_name(SESSION_NAME) or die('Невозможно инициализировать сессии');
@session_start() or die('Невозможно инициализировать сессии');
define('SESS', preg_replace('#[^a-z0-9]#i', '', session_id()));
/* ------------------------------- */


// загрузка основного класса с настройками
$dcms = new dcms ();

debug::step('Загрузка основного класса с настройками');

if (isset($_GET['check_domain_work'])) {
    // проверка доступности поддомена.
    // используется при включении поддомена для определенного типа браузера
    echo $dcms->check_domain_work;
    exit;
}

if ($dcms->subdomain_theme_redirect && empty($subdomain_theme_redirect_disable)) {
    // переадресация на поддомен, соответствующий типу браузера    
    if ($_SERVER['HTTP_HOST'] === $dcms->subdomain_main) {
        // проверяем, что мы находимся на главном домене, а не на поддомене
        // свойство, в котором хранится значение поддомена для данного типа браузера
        $subdomain_var = "subdomain_" . $dcms->browser_type_auto;
        // свойство, в котором хранится парметр, отвечающий за работоспособность домена
        $subdomain_enable = "subdomain_" . $dcms->browser_type_auto . "_enable";

        if ($dcms->$subdomain_enable) {
            // проверяем, включен ли поддомен для данного типа браузера
            // переадресовываем на соответствующий поддомен
            header('Location: ' . (empty($_SERVER['HTTPS']) ? 'http' : 'https') . '://' . $dcms->$subdomain_var . '.' . $dcms->subdomain_main . $_SERVER ['REQUEST_URI']);
            exit;
        }
    }
}


if ($dcms->language && languages::exists($dcms->language)) {
    // системный языковой пакет
    $user_language_pack = new language_pack($dcms->language);
    debug::step('Загрузка системного языкового пакета');
}


// этот параметр будут влиять на счетчики
if ($dcms->new_time_as_date) {
    // новые файлы, темы и т.д. будут отображаться за текущее число
    define('NEW_TIME', DAY_TIME);
} else {
    // новые файлы, темы и т.д. будут отображаться за последние 24 часа
    define('NEW_TIME', TIME - 86400);
}

@mysql_connect($dcms->mysql_host, $dcms->mysql_user, $dcms->mysql_pass) or die('Нет соединения с MySQL сервером');
@mysql_select_db($dcms->mysql_base) or die('Нет доступа к выбранной базе данных');
mysql_query('SET NAMES "utf8"');

debug::step('Подключение к MySQL');

// отправка писем из очереди
mail::queue_process();
debug::step('Отправка писем из очереди');

// запись рефералов
if ($dcms->log_of_referers) {
    $log_of_referers = new log_of_referers ();
    debug::step('Запись рефералов');
}

// запись посещений
if ($dcms->log_of_visits) {
    $log_of_visits = new log_of_visits ();
    // подведение итогов (хоть раз в сутки, чтобы не забивалась таблица)
    if (!cache_events::get('log_of_visits')) {
        cache_events::set('log_of_visits', true, mt_rand(82800, 86400));
        $log_of_visits->tally(); // подведение итогов        
    }
    debug::step('Запись посещений');
}

// Автоматическое обновление системы
if ($dcms->update_auto && $dcms->update_auto_time && !cache_events::get('system.update.auto')) {
    cache_events::set('system.update.auto', true, $dcms->update_auto_time);
    include H . '/sys/inc/update.php';

    $update = new update();
    if (version_compare($update->version, $dcms->version, '>')) {

        if ($dcms->update_auto == 2 && @function_exists('ignore_user_abort') && @function_exists('set_time_limit')) {
            if ($update->start()) {
                // новая версия установлена
                $mess = __('Обновление DCMS (с %s по %s) успешно выполнено', $dcms->version, $update->version);
            } else {
                // при установке новой версии возникла ошибка
                $mess = __('При обновлении DCMS (с %s по %s) произошла ошибка', $dcms->version, $update->version);
            }
        } else {
            $mess = __('Вышла новая версия DCMS: %s. [url=/dpanel/sys.update.php]Обновить[/url]', $update->version);
        }

        $admins = groups::getAdmins();
        foreach ($admins AS $admin) {
            $admin->mess($mess);
        }
    }
    debug::step('Автоматическое обновление системы');
}

// очистка от пользователей, которые не подтвердили регистрацию в течении суток.
if ($dcms->clear_users_not_verify && !cache_events::get('clear_users_not_verify')) {
    cache_events::set('clear_users_not_verify', true, mt_rand(82800, 86400));

    $q = mysql_query("SELECT `id` FROM `users` WHERE `a_code` <> '' AND `reg_date` < '" . (TIME - 86400) . "'");
    if ($count_delete = mysql_num_rows($q)) {
        misc::log('Будет удалено неактивированных пользователей: ' . $count_delete, 'system.users');
        while ($u = mysql_fetch_assoc($q)) {
            misc::user_delete($u['id']);
        }
    }
    debug::step('очистка от пользователей, которые не подтвердили регистрацию в течении суток');
}

// очистка от устаревших временных файлов (чтобы не забивалась папка sys/tmp)
if (!cache_events::get('clear_tmp_dir')) {
    cache_events::set('clear_tmp_dir', true, mt_rand(82800, 86400));
    misc::log('Запускаем удаление временных файлов', 'system.tmp');
    filesystem::deleteOldTmpFiles();
    misc::log('Удаление временных файлов завершено', 'system.tmp');
    debug::step('очистка от устаревших временных файлов');
}

// авторизация пользователя
if (!empty($_SESSION [SESSION_ID_USER])) {

    // авторизация по сессии
    $user = new user($_SESSION [SESSION_ID_USER]);

    if ($user->password !== crypt::hash($_SESSION [SESSION_PASSWORD_USER], $dcms->salt)) {
        $user = new user(false);
        unset($_SESSION [SESSION_ID_USER]);
        unset($_SESSION [SESSION_PASSWORD_USER]);
    }
    debug::step('авторизация пользователя по сессии');
} elseif (!empty($_COOKIE [COOKIE_ID_USER]) && !empty($_COOKIE [COOKIE_USER_PASSWORD]) && $_SERVER ['SCRIPT_NAME'] !== '/login.php' && $_SERVER ['SCRIPT_NAME'] !== '/captcha.php') {
    // авторизация по COOKIE (получение сессии, по которой пользователь авторизуется)
    header('Location: /login.php?cookie&return=' . URL);
    exit;
} else {
    // пользователь будет являться гостем
    $user = new user(false);
    debug::step('авторизация пользователя (гость)');
}

// удаляем сессию пользователя, если по ней не удалось авторизоваться
if ($user->id === false && isset($_SESSION [SESSION_ID_USER])) {
    unset($_SESSION [SESSION_ID_USER]);
}

// удаление пользователей, вышедших из онлайна
mysql_query("DELETE FROM `users_online` WHERE `time_last` < '" . (TIME - SESSION_LIFE_TIME) . "'");
debug::step('удаление пользователей, вышедших из онлайна');


// обработка данных пользователя
if ($user->id !== false) {

    $user->last_visit = TIME; // запись последнего посещения


    if (AJAX) {
        // при AJAX запросе только обновляем сведения о времени последнего посещения, чтобы пользователь оставался в онлайне
        mysql_query("UPDATE `users_online` SET `time_last` = '" . TIME . "' WHERE `id_user` = '$user->id' LIMIT 1");
    } else {

        $user->conversions++; // счетчик переходов

        $q = mysql_query("SELECT * FROM `users_online` WHERE `id_user` = '{$user->id}' LIMIT 1");
        if (mysql_num_rows($q)) {
            mysql_query("UPDATE `users_online` SET `conversions` = `conversions` + '1' , `time_last` = '" . TIME . "', `id_browser` = '$dcms->browser_id', `ip_long` = '$dcms->ip_long', `request` = '" . my_esc($_SERVER ['REQUEST_URI']) . "' WHERE `id_user` = '$user->id' LIMIT 1");
        } else {
            mysql_query("INSERT INTO `users_online` (`id_user`, `time_last`, `time_login`, `request`, `id_browser`, `ip_long`) VALUES ('$user->id', '" . TIME . "', '" . TIME . "', '" . my_esc($_SERVER ['REQUEST_URI']) . "', '$dcms->browser_id', '$dcms->ip_long')");
            $user->count_visit++; // счетчик посещений
        }
    }
} else {
    // обработка гостя
    // зачистка гостей, вышедших из онлайна
    mysql_query("DELETE FROM `guest_online` WHERE `time_last` < '" . (TIME - SESSION_LIFE_TIME) . "'");

    if (!AJAX) {
        // при ajax запросе данные о переходе засчитывать не будем

        $q = mysql_query("SELECT * FROM `guest_online` WHERE `ip_long` = '{$dcms->ip_long}' AND `browser` = '" . my_esc($dcms->browser) . "' LIMIT 1");
        if (mysql_num_rows($q)) {
            // повторные переходы гостя
            mysql_query("UPDATE `guest_online` SET `time_last` = '" . TIME . "', `request` = '" . my_esc($_SERVER ['REQUEST_URI']) . "', `conversions` = `conversions` + 1 WHERE  `ip_long` = '{$dcms->ip_long}' AND `browser` = '{$dcms->browser}' LIMIT 1");
        } else {
            // новый гость
            mysql_query("INSERT INTO `guest_online` (`ip_long`, `browser`, `time_last`, `time_start`, `request` ) VALUES ('{$dcms->ip_long}', '" . my_esc($dcms->browser) . "', '" . TIME . "', '" . TIME . "', '" . my_esc($_SERVER ['REQUEST_URI']) . "')");
        }
    }
}
debug::step('обработка данных пользователя');

if ($user->is_ban_full && $_SERVER ['SCRIPT_NAME'] != '/ban.php') {
    // при полном бане никуда кроме страницы бана нельзя
    header('Location: /ban.php?' . SID);
    exit;
}

// включаем полный показ ошибок для создателя, если включено в админке
if ($dcms->debug && $user->group == groups::max() && @function_exists('ini_set')) {
    ini_set('error_reporting', E_ALL);
    ini_set('display_errors', true);
}

if ($user->language && languages::exists($user->language)) {
    $user_language_pack = new language_pack($user->language);
    debug::step('Загрузка языкового пакета пользователя');
} elseif (!empty($_SESSION['language']) && languages::exists($_SESSION['language'])) {
    $user_language_pack = new language_pack($_SESSION['language']);
    debug::step('Загрузка языкового пакета указанного в сессии');
}
?>