<?php

include_once '../sys/inc/start.php';
dpanel::check_access();
$doc = new document(5);
$doc->title = __('Параметры форума');

if (isset($_POST['save'])) {

    $dcms->forum_theme_captcha = (int) !empty($_POST['forum_theme_captcha']);
    $dcms->forum_message_captcha = (int) !empty($_POST['forum_message_captcha']);

    $dcms->forum_search_captcha = (int) !empty($_POST['forum_search_captcha']);
    $dcms->forum_search_reg = (int) !empty($_POST['forum_search_reg']);

    // максимальный размер файла
    $dcms->forum_files_upload_size = (int) ($_POST['forum_files_upload_size'] * 1024);
    if ($dcms->save_settings()) {
        $doc->msg(__('Настройки успешно сохранены'));
    } else {
        $doc->err(__('Нет прав на запись в файл настроек'));
    }
}

$form = new design();
$form->assign('method', 'post');
$form->assign('action', '?' . passgen());
$elements = array();



$elements[] = array('type' => 'checkbox', 'br' => 1, 'info' => array('value' => 1, 'checked' => $dcms->forum_theme_captcha, 'name' => 'forum_theme_captcha', 'text' => __('Создание тем через капчу') . ' *'));
$elements[] = array('type' => 'checkbox', 'br' => 1, 'info' => array('value' => 1, 'checked' => $dcms->forum_message_captcha, 'name' => 'forum_message_captcha', 'text' => __('Сообщения через капчу') . ' *'));

$elements[] = array('type' => 'input_text', 'title' => __('Макс. размер прикрепляемого файла (KB)'), 'br' => 1, 'info' => array('name' => 'forum_files_upload_size', 'value' => (int) ($dcms->forum_files_upload_size / 1024)));


$elements[] = array('type' => 'checkbox', 'br' => 1, 'info' => array('value' => 1, 'checked' => $dcms->forum_search_captcha, 'name' => 'forum_search_captcha', 'text' => __('Поиск через капчу') ));
$elements[] = array('type' => 'checkbox', 'br' => 1, 'info' => array('value' => 1, 'checked' => $dcms->forum_search_reg, 'name' => 'forum_search_reg', 'text' => __('Поиск только для зарегистрированных')));




$elements[] = array('type' => 'text', 'br' => 1, 'value' => '* ' . __('На администрацию данные ограничения не распространяются'));
$elements[] = array('type' => 'submit', 'br' => 0, 'info' => array('name' => 'save', 'value' => __('Применить'))); // кнопка
$form->assign('el', $elements);
$form->display('input.form.tpl');

$doc->ret(__('Админка'), '/dpanel/');
?>
