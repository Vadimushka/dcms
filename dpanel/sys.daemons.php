<?php

include_once '../sys/inc/start.php';
dpanel::check_access();
$doc = new document(5);
$doc->title = __('Системные службы');

if (isset($_POST['save'])) {
    // журнал посещений
    $dcms->log_of_visits = (int) !empty($_POST['log_of_visits']);
    // журнал рефереров
    $dcms->log_of_referers = (int) !empty($_POST['log_of_referers']);
    // Чистка временной папки
    $dcms->clear_tmp_dir = (int) !empty($_POST['clear_tmp_dir']);

    // режим автоматического обновления
    $dcms->update_auto = min(max($_POST ['update_auto'], 0), 2);

    // периодичность проверки обновлений
    $dcms->update_auto_time = (int) $_POST['update_auto_time'];

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

$elements[] = array('type' => 'checkbox', 'br' => 1, 'info' => array('value' => 1, 'checked' => $dcms->log_of_visits, 'name' => 'log_of_visits', 'text' => __('Журнал посещений')));
$elements[] = array('type' => 'checkbox', 'br' => 1, 'info' => array('value' => 1, 'checked' => $dcms->log_of_referers, 'name' => 'log_of_referers', 'text' => __('Журнал рефереров')));


$options = array();
$options[] = array('3600', __('Раз в час'), $dcms->update_auto_time == '3600');
$options[] = array('21600', __('Раз в 6 часов'), $dcms->update_auto_time == '21600');
$options[] = array('43200', __('Раз в 12 часов'), $dcms->update_auto_time == '43200');
$options[] = array('86400', __('Раз в сутки'), $dcms->update_auto_time == '86400');
$elements[] = array('type' => 'select', 'title' => __('Периодичность проверки новой версии'), 'br' => 1, 'info' => array('name' => 'update_auto_time', 'options' => $options));


$options = array();
$options[] = array('0', __('Отключено'), $dcms->update_auto == '0');
$options[] = array('1', __('Уведомлять о новой версии'), $dcms->update_auto == '1');
$options[] = array('2', __('Устанавливать новую версию'), $dcms->update_auto == '2');
$elements[] = array('type' => 'select', 'title' => __('Автоматическое обновление'), 'br' => 1, 'info' => array('name' => 'update_auto', 'options' => $options));



$elements[] = array('type' => 'submit', 'br' => 0, 'info' => array('name' => 'save', 'value' => __('Применить'))); // кнопка
$form->assign('el', $elements);
$form->display('input.form.tpl');

$doc->ret(__('Админка'), '/dpanel/');
?>
