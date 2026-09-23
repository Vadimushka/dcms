<?php

include_once '../sys/inc/start.php';
dpanel::check_access();
$doc = new document(5);
$doc->title = __('Системные службы');

if (isset($_POST['save'])) {
    $dcms->log_of_visits = (int) !empty($_POST['log_of_visits']);
    $dcms->log_of_referers = (int) !empty($_POST['log_of_referers']);
    $dcms->clear_tmp_dir = (int) !empty($_POST['clear_tmp_dir']);
    $dcms->update_auto = (int) !empty($_POST['update_auto']);
    $dcms->update_auto_time = (int) $_POST['update_auto_time'];
    $dcms->update_url = Dcms\Helpers\UpdateCheck::normalizeUrl(isset($_POST['update_url']) ? $_POST['update_url'] : '');
    $dcms->save_settings($doc);
}


$form = new form('?' . passgen());
$form->checkbox('log_of_visits', __('Журнал посещений'), $dcms->log_of_visits);
$form->checkbox('log_of_referers', __('Журнал рефереров'), $dcms->log_of_referers);
$form->checkbox('clear_tmp_dir', __('Чистка папки с временными файлами'), $dcms->clear_tmp_dir);

$options = array();
$options[] = array('3600', __('Раз в час'), $dcms->update_auto_time == '3600');
$options[] = array('21600', __('Раз в 6 часов'), $dcms->update_auto_time == '21600');
$options[] = array('43200', __('Раз в 12 часов'), $dcms->update_auto_time == '43200');
$options[] = array('86400', __('Раз в сутки'), $dcms->update_auto_time == '86400');

$form->select('update_auto_time', __('Периодичность проверки новой версии'), $options);

$options = array();
$options[] = array('0', __('Отключено'), !$dcms->update_auto);
$options[] = array('1', __('Уведомлять о новой версии'), (bool) $dcms->update_auto);
$form->select('update_auto', __('Проверка обновлений'), $options);

$form->text('update_url', __('Где искать новые версии'), Dcms\Helpers\UpdateCheck::normalizeUrl($dcms->update_url));
$form->bbcode(__('Движок только сообщает о выпуске новой версии: файлы обновления администратор заливает сам. Своя сборка проверяется по своему репозиторию — [b]https://api.github.com/repos/<владелец>/<репозиторий>/releases/latest[/b].'));

$form->button(__('Применить'), 'save');
$form->display();

$doc->ret(__('Админка'), '/dpanel/');