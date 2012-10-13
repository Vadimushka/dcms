<?php

include_once '../sys/inc/start.php';
$doc = new document(5);
$doc->title = __('Статистика (сегодня)');

if (!$dcms->log_of_visits) {
    $doc->err(__('Служба ведения статистики отключена'));
    $doc->act(__('Управление службами'), 'sys.settings.daemons.php');
}


$hits = array();
$hits['wap'] = mysql_result(mysql_query("SELECT COUNT(*) FROM `log_of_visits_today` WHERE `time` = '" . DAY_TIME . "' AND `browser_type` = 'wap'"), 0);
$hits['pda'] = mysql_result(mysql_query("SELECT COUNT(*) FROM `log_of_visits_today` WHERE `time` = '" . DAY_TIME . "' AND `browser_type` = 'pda'"), 0);
$hits['itouch'] = mysql_result(mysql_query("SELECT COUNT(*) FROM `log_of_visits_today` WHERE `time` = '" . DAY_TIME . "' AND `browser_type` = 'itouch'"), 0);
$hits['web'] = mysql_result(mysql_query("SELECT COUNT(*) FROM `log_of_visits_today` WHERE `time` = '" . DAY_TIME . "' AND `browser_type` = 'web'"), 0);


$hosts = array();
$hosts['wap'] = mysql_result(mysql_query("SELECT COUNT(DISTINCT `iplong` , `id_browser`) FROM `log_of_visits_today` WHERE `time` = '" . DAY_TIME . "' AND `browser_type` = 'wap'"), 0);
$hosts['pda'] = mysql_result(mysql_query("SELECT COUNT(DISTINCT `iplong` , `id_browser`) FROM `log_of_visits_today` WHERE `time` = '" . DAY_TIME . "' AND `browser_type` = 'pda'"), 0);
$hosts['itouch'] = mysql_result(mysql_query("SELECT COUNT(DISTINCT `iplong` , `id_browser`) FROM `log_of_visits_today` WHERE `time` = '" . DAY_TIME . "' AND `browser_type` = 'itouch'"), 0);
$hosts['web'] = mysql_result(mysql_query("SELECT COUNT(DISTINCT `iplong` , `id_browser`) FROM `log_of_visits_today` WHERE `time` = '" . DAY_TIME . "' AND `browser_type` = 'web'"), 0);

if (isset($log_of_visits) && mysql_result(mysql_query("SELECT COUNT(*) FROM `log_of_visits_today` WHERE `time` <> '" . DAY_TIME . "' LIMIT 1"), 0)) {
    $log_of_visits->tally();
}


echo __('Кол-во переходов') . ":<br />";
echo __('WAP: %s', $hits['wap']) . '<br />';
echo __('PDA: %s', $hits['pda']) . '<br />';
echo __('iTouch: %s', $hits['itouch']) . '<br />';
echo __('WEB: %s', $hits['web']) . '<br />';

echo "<br />";

echo __('Кол-во уникальных посетителей') . ":<br />";
echo __('WAP: %s', $hosts['wap']) . '<br />';
echo __('PDA: %s', $hosts['pda']) . '<br />';
echo __('iTouch: %s', $hosts['itouch']) . '<br />';
echo __('WEB: %s', $hosts['web']) . '<br />';


$doc->ret(__('Админка'), './');
?>
