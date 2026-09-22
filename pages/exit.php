<?php

$subdomain_theme_redirect_disable = true; // принудительное отключение редиректа на поддомены, соответствующие типу браузера
include_once '../sys/inc/start.php';
$doc = new document(1);
$doc->title = __('Выход');

if (isset($_POST['exit'])){
    $res = $db->prepare("DELETE FROM `users_online` WHERE `id_user` = ?;");
    $res->execute(Array($user->id));
    
    $user->guest_init();
    
    if (!empty($_COOKIE[COOKIE_USER_TOKEN]))
        user_token::delete($_COOKIE[COOKIE_USER_TOKEN]);
    user_token::clearCookie();
    setcookie(COOKIE_ID_USER, '', array('expires' => TIME - 3600, 'path' => '/'));
    unset($_SESSION);
    session_destroy();

    /* Инициализация механизма сессий  */
    session_name(SESSION_NAME) or die(__('Невозможно инициализировать сессии'));
    @session_start() or die(__('Невозможно инициализировать сессии'));
    // иначе на общем устройстве прежний идентификатор остаётся действующим
    session_regenerate_id(true);

    $doc->msg(__('Авторизация успешно сброшена'));
    exit;
}

$form = new form('?');
$form->bbcode(__("Вы действительно хотите сбросить авторизацию?"));
$form->button(__("Выйти"), 'exit');
$form->display();
