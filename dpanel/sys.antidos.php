<?php

include_once '../sys/inc/start.php';
dpanel::check_access();
$doc = new document(5);
$doc->title = __('Anti-DoS защита');

if (isset($_POST['save'])) {
    // включение
    $dcms->antidos = (int) !empty($_POST['antidos']);

    if (empty($_POST['antidos_period'])) {
        $doc->err(__('Не выбран период слежения'));
        $dcms->antidos = 0; // отключаем от греха
    }

    $dcms->antidos_period = (int) $_POST['antidos_period'];

    if ($dcms->antidos_period < 5) {
        $doc->err(__('Период слежения не может быть менее 5 секунд (будет много ложных срабатываний)'));
        $dcms->antidos = 0; // отключаем от греха
    }

    if ($dcms->antidos_period > 600) {
        $doc->err(__('Слишком большой период слежения (более 10 мин) будет не эффективен'));
        $dcms->antidos = 0; // отключаем от греха
    }

    if (empty($_POST['antidos_max_visits_per_second'])) {
        $doc->err(__('Не выбрано максимальное кол-во посещений в секунду'));
        $dcms->antidos = 0; // отключаем от греха
    }

    $dcms->antidos_max_visits_per_second = (int) $_POST['antidos_max_visits_per_second'];

    if ($dcms->antidos_max_visits_per_second < 1) {
        $doc->err(__('Нельзя ставить менее 1 перехода в секунду (как же тогда по сайту передвигаться?)'));
        $dcms->antidos = 0; // отключаем от греха
    }

    if ($dcms->antidos_max_visits_per_second > 30) {
        $doc->err(__('Нельзя ставить более 30 переходов в секунду (больно дохрена для нормального серфинга)'));
        $dcms->antidos = 0; // отключаем от греха
    }

    if (empty($_POST['antidos_visits_per_second_for_ban'])) {
        $doc->err(__('Не выбрано максимальное кол-во посещений в секунду для бана'));
        $dcms->antidos = 0; // отключаем от греха
    }

    $dcms->antidos_visits_per_second_for_ban = (int) $_POST['antidos_visits_per_second_for_ban'];

    if ($dcms->antidos_visits_per_second_for_ban < 2) {
        $doc->err(__('Нельзя ставить менее 2-х переходов в секунду для бана (это не DoS совсем)'));
        $dcms->antidos = 0; // отключаем от греха
    }

    if ($dcms->antidos_visits_per_second_for_ban < $dcms->antidos_max_visits_per_second) {
        $doc->err(__('Кол-во переходов в секунду для бана не может быть больше чем у простой защиты'));
        $dcms->antidos = 0; // отключаем от греха
    }

    if ($dcms->antidos_max_visits_per_second > 60) {
        $doc->err(__('Нельзя ставить более 60 переходов в секунду для бана (больно дохрена для нормального серфинга)'));
        $dcms->antidos = 0; // отключаем от греха
    }

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

$elements[] = array('type' => 'checkbox', 'br' => 1, 'info' => array('value' => 1, 'checked' => $dcms->antidos, 'name' => 'antidos', 'text' => __('Включить Anti-DoS')));

$elements[] = array('type' => 'input_text', 'title' => __('Период слежения (сек)') . ' [5 - 600]', 'br' => 1, 'info' => array('name' => 'antidos_period', 'value' => $dcms->antidos_period));

$elements[] = array('type' => 'input_text', 'title' => __('Макс. кол-во посещений в секунду') . ' [1 - 30] *', 'br' => 1, 'info' => array('name' => 'antidos_max_visits_per_second', 'value' => $dcms->antidos_max_visits_per_second));

$elements[] = array('type' => 'input_text', 'title' => __('Макс. кол-во посещений в секунду (бан по IP)') . ' [2 - 60]**', 'br' => 1, 'info' => array('name' => 'antidos_visits_per_second_for_ban', 'value' => $dcms->antidos_visits_per_second_for_ban));

$elements[] = array('type' => 'text', 'br' => 1, 'value' => '* ' . __('Берется среднее значение за период'));
$elements[] = array('type' => 'text', 'br' => 1, 'value' => '** ' . __('Бан по IP адресу на 10 периодов'));

$elements[] = array('type' => 'submit', 'br' => 0, 'info' => array('name' => 'save', 'value' => __('Применить'))); // кнопка
$form->assign('el', $elements);
$form->display('input.form.tpl');

$doc->ret(__('Админка'), './');
?>