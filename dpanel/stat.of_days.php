<?php

include_once '../sys/inc/start.php';
$doc = new document(5);
$doc->title = __('Статистика (по дням)');

if (!$dcms->log_of_visits) {
    $doc->err(__('Служба ведения статистики отключена'));
}

$res = $db->prepare("SELECT COUNT(*) FROM `log_of_visits_today` WHERE `time` <> ? LIMIT 1");
$res->execute(Array(DAY_TIME));
$k = $res->fetchColumn();
if (isset($log_of_visits) && $k) {
    $log_of_visits->tally();
}

$res = $db->query("SELECT COUNT(*) FROM `log_of_visits_for_days`");
$pages = new pages;
$pages->posts = $res->fetchColumn(); // количество сообщений

$listing = new listing();
$q = $db->query("SELECT * FROM `log_of_visits_for_days` ORDER BY `time_day` DESC LIMIT ".$pages->limit);
while ($st = $q->fetch()) {
    $post = $listing->post();
    $post->title = date('d-m-Y', $st['time_day']);
    $post->icon('statistics');
    $post->content = "<table border='1' style='border-collapse: collapse'>\n";
    $post->content .= "<tr><td></td><td>WAP</td><td>PDA</td><td>iTouch</td><td>WEB</td><td>" . __('В сумме') . "</td></tr>\n";
    $post->content .= "<tr><td>" . __('Хосты') . "</td><td>$st[hosts_light]</td><td>$st[hosts_mobile]</td><td>$st[hosts_full]</td><td>" . ($st['hosts_light'] + $st['hosts_mobile'] + $st['hosts_full']) . "</td></tr>\n";
    $post->content .= "<tr><td>" . __('Хиты') . "</td><td>$st[hits_light]</td><td>$st[hits_mobile]</td><td>$st[hits_full]</td><td>" . ($st['hits_light'] + $st['hits_mobile'] + $st['hits_full']) . "</td></tr>\n";
    $post->content .= "</table>\n";
}
$listing->display(__('Сообщения отсутствуют'));

$pages->display('?');

if (!$dcms->log_of_visits) {
    $doc->act(__('Управление службами'), 'sys.settings.daemons.php');
}

$doc->ret(__('Админка'), './');