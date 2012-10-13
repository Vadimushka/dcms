<?php

error_reporting(E_ALL);

if (@function_exists('set_time_limit')) {
    @set_time_limit(600);
}

// проверка операционной системы
if (!defined('IS_WINDOWS')) {
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        define('IS_WINDOWS', true);
    } else {
        define('IS_WINDOWS', false);
    }
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


define('TEMP', H . '/sys/tmp'); // временная папка
define('TMP', H . '/sys/tmp'); // временная папка
define('TIME', time()); // временная метка unix

define('FILES', realpath(H . '/sys/files'));
define('SESSION_NAME', 'DCMS_INSTALL_SESSION');





if (version_compare(PHP_VERSION, '5.4', '>=')) {
    define('HTMLENT_OPTIONS', ENT_QUOTES | ENT_SUBSTITUTE | ENT_XHTML);
} elseif (version_compare(PHP_VERSION, '5.3', '>=')) {
    define('HTMLENT_OPTIONS', ENT_QUOTES | ENT_IGNORE);
} else {
    define('HTMLENT_OPTIONS', ENT_QUOTES);
}

function __autoload($class_name) {
    @include_once (H . '/sys/plugins/classes/' . strtolower($class_name) . '.class.php');
}
include_once (H . '/sys/plugins/classes/cache.class.php');

session_name(SESSION_NAME) or die('Невозможно инициализировать сессии');
@session_start() or die('Невозможно инициализировать сессии');
define('SESS', preg_replace('#[^a-z0-9]#i', '', session_id()));

ini_set('display_errors', true);
ini_set('ignore_repeated_errors', true);


if (!empty($_SESSION['language']) && languages::exists($_SESSION['language'])) {
    $user_language_pack = new language_pack($_SESSION['language']);
} else {
    $user_language_pack = new language_pack(false);
}

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

function my_esc($str) {
    return mysql_real_escape_string($str);
}

function for_value($text) {
    return text::for_value($text);
}

function output_text($text) {
    return text::output_text($text);
}

function rus_mes($mes, $v = 1) {
    return misc::rus_mes($mes, $v);
}

function vremja($t) {
    return misc::vremja($t);
}

function size_data($filesize = 0) {
    return misc::size_data($filesize);
}

/* ---------------------- */

function myhash($pass, $salt) {
    return md5($salt . md5((string) $pass) . md5($salt) . $salt);
}

function db_connect() {
    $settings = &$_SESSION['settings'];
    mysql_connect($settings['mysql_host'], $settings['mysql_user'], $settings['mysql_pass']) or die(__('Нет соединения с сервером базы'));
    mysql_select_db($settings['mysql_base']) or die(__('Нет доступа к выбранной базе данных'));
    mysql_query('SET NAMES "utf8"');
}

function passgen($len = 32) {
    $password = '';
    $small = 'abcdefghijklmnopqrstuvwxyz';
    $large = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $numbers = '1234567890';
    // $symbols='_)(%^*!-';
    for ($i = 0; $i < $len; $i++) {
        switch (mt_rand(1, 3)) {
            // case 4:$password.=$symbols[mt_rand(0,8)];break;
            case 3:$password .= $large[mt_rand(0, 25)];
                break;
            case 2:$password .= $small[mt_rand(0, 25)];
                break;
            case 1:$password .= $numbers[mt_rand(0, 9)];
                break;
        }
    }
    return $password;
}

if (is_file(H . '/sys/ini/settings.ini')) {
    header("Location: /?" . passgen() . '&' . SID);
    exit;
}

$install = &$_SESSION['install'];
$options = &$_SESSION['options'];
$ini = @parse_ini_file('inc/steps.ini', true);

foreach ($ini as $key => $value) {
    if (empty($value['if_option']) || isset($options[$value['if_option']])) {
        if (empty($install[$key]['status'])) {
            $step = $key;
            break;
        }
    }
}

header('Content-Type: application/xhtml+xml; charset=utf-8');
ob_start();
include 'inc/head.php';

echo "<h1>" . __($ini[$step]['title']) . "</h1>";

if (isset($_POST['to_start'])) {
    header("Location: ./?" . passgen() . '&' . SID);

    unset($_SESSION);
    session_destroy();

    /* Инициализация механизма сессий  */
    session_name(SESSION_NAME) or die(__('Невозможно инициализировать сессии'));
    @session_start() or die(__('Невозможно инициализировать сессии'));
    exit;
}
echo "<form method='post' action='?" . passgen() . "'>";
echo "<input type='submit' name='to_start' value='" . __('В начало') . "' />";
echo "<input type='submit' name='refresh' value='" . __('Обновить') . "' />";
echo "</form>";

if (!@include_once ('inc/' . $step . '.php'))
    die(__('Невозможно продолжить установку по причине отсутствия файла %s', 'inc/' . $step . '.php'));
$inst_obj = new $step;

if (isset($_POST['next_step'])) {
    $install[$step]['status'] = $inst_obj->actions();

    header("Location: ./?" . passgen() . '&' . SID);
    exit;
}

echo "<form method='post' action='?" . passgen() . "'>";
if ($inst_obj->form())
    echo "<input type='submit' name='next_step' value='" . __('Далее') . "' />";
else
    echo "<input type='submit' name='refresh' value='" . __('Обновить') . "' />";
echo "</form>";

include 'inc/foot.php';
?>
