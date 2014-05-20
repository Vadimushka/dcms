<?php

include_once '../sys/inc/start.php';
$doc = new document(5);
$doc->title = __('Статистика (сегодня)');

$browser_types = array('wap', 'pda', 'itouch', 'web');

if (!$dcms->log_of_visits) {
    $doc->err(__('Служба ведения статистики отключена'));
    $doc->act(__('Управление службами'), 'sys.settings.daemons.php');
}

$arr = Array('wap', 'pda', 'itouch', 'web');
$hits = array();
$hosts = array();
$res_hits = $db->prepare("SELECT COUNT(*) AS cnt FROM `log_of_visits_today` WHERE `time` = ? AND `browser_type` = ?");
$res_hosts = $db->prepare("SELECT COUNT(DISTINCT `iplong` , `id_browser`) AS cnt FROM `log_of_visits_today` WHERE `time` = ? AND `browser_type` = ?");
foreach ($arr AS $val) {
    $res_hits->execute(Array(DAY_TIME, $val));
    $hits[$val] = ($row = $res_hits->fetch()) ? $row['cnt'] : 0;
    $res_hosts->execute(Array(DAY_TIME, $val));
    $hosts[$val] = ($row = $res_hosts->fetch()) ? $row['cnt'] : 0;
}

$res = $db->prepare("SELECT COUNT(*) AS cnt FROM `log_of_visits_today` WHERE `time` <> ? LIMIT 1");
$res->execute(Array(DAY_TIME));
$k = ($row = $res->fetch()) ? $row['cnt'] : 0;
if (isset($log_of_visits) && $k) {
    $log_of_visits->tally();
}

$listing = new listing();
$post = $listing->post();
$post->title = __('Кол-во переходов');
$post->icon('info');
$post->hightlight = true;

foreach ($browser_types AS $b_type) {
    $post = $listing->post();
    $post->title = strtoupper($b_type);
    $post->content = __('%s переход' . misc::number($hits[$b_type], '', 'а', 'ов'), $hits[$b_type]);
}

$post = $listing->post();
$post->title = __('Всего переходов');
$post->content = __('%s переход' . misc::number(array_sum($hits), '', 'а', 'ов'), array_sum($hits));


$listing->display();


$listing = new listing();
$post = $listing->post();
$post->title = __('Уникальные посетители');
$post->icon('info');
$post->hightlight = true;

foreach ($browser_types AS $b_type) {
    $post = $listing->post();
    $post->title = strtoupper($b_type);
    $post->content = __('%s посетител' . misc::number($hosts[$b_type], 'ь', 'я', 'ей'), $hosts[$b_type]);
}

$post = $listing->post();
$post->title = __('Всего посетителей');
$post->content = __('%s посетител' . misc::number(array_sum($hosts), 'ь', 'я', 'ей'), array_sum($hosts));

$listing->display();

$doc->ret(__('Админка'), './');
