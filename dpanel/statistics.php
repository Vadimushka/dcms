<?php
include '../sys/inc/start.php';
$doc = new document(5);
$doc->title = __('Статистика');

if (!$dcms->log_of_visits) {
    $doc->err(__('Служба ведения статистики отключена'));
}

$prep = db::me()->prepare("SELECT
(SELECT COUNT(*) FROM `log_of_visits_today` WHERE `browser_type` = 'full' AND `time` > :time_start AND `time` <= :time_end) AS `hits_full`,
             (SELECT COUNT(*) FROM `log_of_visits_today` WHERE `browser_type` = 'mobile' AND `time` > :time_start AND `time` <= :time_end) AS `hits_mobile`,
             (SELECT COUNT(*) FROM `log_of_visits_today` WHERE `browser_type` = 'lite' AND `time` > :time_start AND `time` <= :time_end) AS `hits_lite`,
             (SELECT COUNT(DISTINCT `iplong` , `id_browser`) FROM `log_of_visits_today` WHERE `browser_type` = 'full' AND `time` > :time_start AND `time` <= :time_end) AS `hosts_full`,
             (SELECT COUNT(DISTINCT `iplong` , `id_browser`) FROM `log_of_visits_today` WHERE `browser_type` = 'mobile' AND `time` > :time_start AND `time` <= :time_end) AS `hosts_mobile`,
             (SELECT COUNT(DISTINCT `iplong` , `id_browser`) FROM `log_of_visits_today` WHERE `browser_type` = 'lite' AND `time` > :time_start AND `time` <= :time_end) AS `hosts_lite`
              ");

$chart = new line_chart(__("За последние сутки"));
$chart->series[] = $s_hosts_full = new line_chart_series(__('Посетители %s', 'WEB'));
$chart->series[] = $s_hosts_mobile = new line_chart_series(__('Посетители %s', 'Mobile'));
$chart->series[] = $s_hosts_lite = new line_chart_series(__('Посетители %s', 'Lite'));
$chart->series[] = $s_hits_full = new line_chart_series(__('Переходы %s', 'WEB'));
$chart->series[] = $s_hits_mobile = new line_chart_series(__('Переходы %s', 'Mobile'));
$chart->series[] = $s_hits_lite = new line_chart_series(__('Переходы %s', 'Lite'));
$i = 24;
while ($i--) {
    $time = TIME - $i * 3600;
    $prep->execute(array(':time_start' => $time - $i * 3600, ':time_end' => $time));
    $data = $prep->fetch();

    $chart->categories[] = date('H', $time + current_user::getInstance()->time_shift);

    $s_hosts_full->data[] = (int)$data['hosts_full'];
    $s_hosts_mobile->data[] = (int)$data['hosts_mobile'];
    $s_hosts_lite->data[] = (int)$data['hosts_lite'];

    $s_hits_full->data[] = (int)$data['hits_full'];
    $s_hits_mobile->data[] = (int)$data['hits_mobile'];
    $s_hits_lite->data[] = (int)$data['hits_lite'];
}

$chart->display();

$res = db::me()->prepare("SELECT * FROM `log_of_visits_for_days` ORDER BY `time_day` DESC LIMIT 30");

$chart = new line_chart(__("За последний месяц"));
$chart->series[] = $s_hosts_full = new line_chart_series(__('Посетители %s', 'WEB'));
$chart->series[] = $s_hosts_mobile = new line_chart_series(__('Посетители %s', 'Mobile'));
$chart->series[] = $s_hosts_lite = new line_chart_series(__('Посетители %s', 'Lite'));
$chart->series[] = $s_hits_full = new line_chart_series(__('Переходы %s', 'WEB'));
$chart->series[] = $s_hits_mobile = new line_chart_series(__('Переходы %s', 'Mobile'));
$chart->series[] = $s_hits_lite = new line_chart_series(__('Переходы %s', 'Lite'));

while ($data = $res->fetch()) {
    $chart->categories[] = date('d', $data['time_day']);

    $s_hosts_full->data[] = (int)$data['hosts_full'];
    $s_hosts_mobile->data[] = (int)$data['hosts_mobile'];
    $s_hosts_lite->data[] = (int)$data['hosts_lite'];

    $s_hits_full->data[] = (int)$data['hits_full'];
    $s_hits_mobile->data[] = (int)$data['hits_mobile'];
    $s_hits_lite->data[] = (int)$data['hits_lite'];
}

$chart->display();