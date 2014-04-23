<?php

$subdomain_theme_redirect_disable = true; // принудительное отключение редиректа на поддомены, соответствующие типу браузера
include_once '../sys/inc/start.php';
$doc = new document(1);
$doc->title = __('Выход');

$res = $db->prepare("DELETE FROM `users_online` WHERE `id_user` = ?;");
$res->execute(Array($user->id));

setcookie(COOKIE_ID_USER);
setcookie(COOKIE_USER_PASSWORD);
unset($_SESSION);
session_destroy();

/* Инициализация механизма сессий  */
session_name(SESSION_NAME) or die(__('Невозможно инициализировать сессии'));
@session_start() or die(__('Невозможно инициализировать сессии'));

/*
if (isset($_GET['return'])) {
    header('Refresh: 1; url=' . $_GET['return']);
} else {
    header('Refresh: 1; url=/');
}
*/
$doc->msg(__('Авторизация успешно сброшена'));

echo __("Будем рады видеть Вас снова") . "<br />\n";
