<?php

include_once '../sys/inc/start.php';
$doc = new document();
$doc->title = 'Вход в панель управления';
$isp = new ispmanager();

if ($isp->isAutorized()) {
    $doc->msg('Вы уже авторизованы');
    $listing = new listing();
    $post = $listing->post();
    $post->url = 'index.php';
    $post->title = 'Панель управления';
    $listing->display();
    exit;
}

if (isset($_POST['login']) && isset($_POST['password'])) {
    if (!$_POST['login'])
        $doc->err(__('Введите логин'));
    elseif (!$_POST['password'])
        $doc->err(__('Введите пароль'));
    elseif (!$isp->login($_POST['login'], $_POST['password'])) {
        $doc->err('Неправильный логин или пароль');
    } elseif (!$isp->isAutorized()) {
        $doc->err('Ошибка при авторизации');
    } else {
        $doc->msg('Вы успешно авторизованы');
        $listing = new listing();
        $post = $listing->post();
        $post->url = 'index.php';
        $post->title = 'Панель управления';
        $listing->display();
        exit;
    }
}


$form = new design();
$form->assign('method', 'post');
$form->assign('action', '?' . passgen());
$elements = array();
$elements[] = array('type' => 'input_text', 'title' => __('Логин'), 'br' => 1, 'info' => array('name' => 'login'));
$elements[] = array('type' => 'password', 'title' => __('Пароль'), 'br' => 1, 'info' => array('name' => 'password'));
$elements [] = array('type' => 'text', 'br' => 1, 'value' => __('Вводите данные от своей учетной записи в ISPmanager'));

$elements[] = array('type' => 'submit', 'br' => 0, 'info' => array('value' => __('Авторизация'))); // кнопка
$form->assign('el', $elements);
$form->display('input.form.tpl');
?>
