<?php

include_once '../sys/inc/start.php';
dpanel::check_access();
$doc = new document(5);
$doc->title = __('Виджеты');
$doc->ret(__('Админка'), '/dpanel/');

$types = array('wap', 'pda', 'itouch', 'web');

if (isset($_POST ['save'])) {

    foreach ($types AS $type) {
        $prop_name = "widget_items_count_" . $type;
        $dcms->$prop_name = min(max((int) $_POST [$prop_name], 0), 50);
    }

    if ($dcms->save_settings()) {
        $doc->msg(__('Настройки успешно сохранены'));
    } else {
        $doc->err(__('Нет прав на запись в файл настроек'));
    }
}

$form = new design ();
$form->assign('method', 'post');
$form->assign('action', '?' . passgen());
$elements = array();

foreach ($types AS $type) {
    $prop_name = "widget_items_count_" . $type;
    $elements [] = array('type' => 'input_text', 'title' => __('Макс. кол-во пунктов в виджете') . ' [0-50] (' . strtoupper($type) . ')', 'br' => 1, 'info' => array('name' => $prop_name, 'value' => $dcms->$prop_name));
}

$elements [] = array('type' => 'submit', 'br' => 0, 'info' => array('name' => 'save', 'value' => __('Применить'))); // кнопка
$form->assign('el', $elements);
$form->display('input.form.tpl');

$doc->ret(__('Админка'), '/dpanel/');
?>