<?php

include_once '../sys/inc/start.php';
dpanel::check_access();
$doc = new document(5);
$doc->title = __('Параметры регистрации');

if (isset($_POST['save'])) {
    // регистрация открыта
    $dcms->reg_open = (int) !empty($_POST['reg_open']);
    // подтверждение регистрации по email
    $dcms->reg_with_mail = (int) !empty($_POST['reg_with_mail']);
    // удаление неактивированных
    $dcms->clear_users_not_verify = (int) !empty($_POST['clear_users_not_verify']);
    
    // соглашение с правилами
    $dcms->reg_with_rules = (int) !empty($_POST['reg_with_rules']);
    // только по пригласительным
    $dcms->reg_with_invite = (int) !empty($_POST['reg_with_invite']);
    // стоимость одного пригласительного
    $dcms->balls_for_invite = (int) $_POST['balls_for_invite'];
    
// ограничение на сообщения в часах
    $dcms->user_write_limit_hour = (int) $_POST['user_write_limit_hour'];
    if ($dcms->save_settings())
        $doc->msg(__('Настройки успешно сохранены'));
    else
        $doc->err(__('Нет прав на запись в файл настроек'));
}

$form = new design();
$form->assign('method', 'post');
$form->assign('action', '?' . passgen());
$elements = array();

$elements[] = array('type' => 'checkbox', 'br' => 1, 'info' => array('value' => 1, 'checked' => $dcms->reg_open, 'name' => 'reg_open', 'text' => __('Разрешить регистрацию')));

$elements[] = array('type' => 'checkbox', 'br' => 1, 'info' => array('value' => 1, 'checked' => $dcms->reg_with_mail, 'name' => 'reg_with_mail', 'text' => __('Активация по E-mail')));

$elements[] = array('type' => 'checkbox', 'br' => 1, 'info' => array('value' => 1, 'checked' => $dcms->clear_users_not_verify, 'name' => 'clear_users_not_verify', 'text' => __('Удалять неактивированных пользователей более суток')));


$elements[] = array('type' => 'checkbox', 'br' => 1, 'info' => array('value' => 1, 'checked' => $dcms->reg_with_invite, 'name' => 'reg_with_invite', 'text' => __('Только по пригласительным')));
$elements [] = array('type' => 'input_text', 'title' => __('Стоимость одного пригласительного (баллы)'), 'br' => 1, 'info' => array('name' => 'balls_for_invite', 'value' => $dcms->balls_for_invite));



$elements[] = array('type' => 'checkbox', 'br' => 1, 'info' => array('value' => 1, 'checked' => $dcms->reg_with_rules, 'name' => 'reg_with_rules', 'text' => __('Соглашение с правилами')));


$elements [] = array('type' => 'input_text', 'title' => __('Разрешено писать через (часы) после регистрации'), 'br' => 1, 'info' => array('name' => 'user_write_limit_hour', 'value' => $dcms->user_write_limit_hour));


$elements[] = array('type' => 'submit', 'br' => 0, 'info' => array('name' => 'save', 'value' => __('Применить'))); // кнопка
$form->assign('el', $elements);
$form->display('input.form.tpl');

$doc->ret(__('Админка'), '/dpanel/');
?>
