<?php

include_once '../sys/inc/start.php';
$doc = new document(5);
$doc->title = __('Статистика (по дням)');

if (!$dcms->log_of_visits) {
    $doc->err(__('Служба ведения статистики отключена'));
}

$res = $db->prepare("SELECT COUNT(*) AS cnt FROM `log_of_visits_today` WHERE `time` <> ? LIMIT 1");
$res->execute(Array(DAY_TIME));
$k = ($row = $res->fetch()) ? $row['cnt'] : 0;
if (isset($log_of_visits) && $k) {
    $log_of_visits->tally();
}

$res = $db->query("SELECT COUNT(*) AS cnt FROM `log_of_visits_for_days`");
$pages = new pages;
$pages->posts = ($row = $res->fetch()) ? $row['cnt'] : 0; // количество сообщений

$listing = new listing();
$q = $db->query("SELECT * FROM `log_of_visits_for_days` ORDER BY `time_day` DESC LIMIT $pages->limit");
while ($st = $q->fetch()) {
    $post = $listing->post();
    $post->title = date('d-m-Y', $st['time_day']);
    $post->icon('statistics');
    $post->content = "<table border='1' style='border-collapse: collapse'>\n";
    $post->content .= "<tr><td></td><td>WAP</td><td>PDA</td><td>iTouch</td><td>WEB</td><td>" . __('В сумме') . "</td></tr>\n";
    $post->content .= "<tr><td>" . __('Хосты') . "</td><td>$st[hosts_wap]</td><td>$st[hosts_pda]</td><td>$st[hosts_itouch]</td><td>$st[hosts_web]</td><td>" . ($st['hosts_wap'] + $st['hosts_pda'] + $st['hosts_itouch'] + $st['hosts_web']) . "</td></tr>\n";
    $post->content .= "<tr><td>" . __('Хиты') . "</td><td>$st[hits_wap]</td><td>$st[hits_pda]</td><td>$st[hits_itouch]</td><td>$st[hits_web]</td><td>" . ($st['hits_wap'] + $st['hits_pda'] + $st['hits_itouch'] + $st['hits_web']) . "</td></tr>\n";
    $post->content .= "</table>\n";
}
$listing->display(__('Сообщения отсутствуют'));

$pages->display('?');

if (!$dcms->log_of_visits) {
    $doc->act(__('Управление службами'), 'sys.settings.daemons.php');
}

$doc->ret(__('Админка'), './');