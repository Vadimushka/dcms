<?php

include_once '../sys/inc/start.php';
//dpanel::check_access();
$advertisement = new adt();
$doc = new document(5);
$doc->title = __('Статистика по рекламе');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Refresh: 1; url=adt.settings.php');
    $doc->ret(__('Реклама и баннеры'), 'adt.php');
    $doc->ret(__('Админка'), '/dpanel/');
    $doc->err(__('Ошибка выбора рекламы'));
    exit;
}
$id_adt = (int) $_GET['id'];

$q = mysql_query("SELECT * FROM `advertising` WHERE `id` = '$id_adt'");

if (!mysql_num_rows($q)) {
    header('Refresh: 1; url=adt.php?id=' . $id_adt);
    $doc->ret(__('Вернуться'), 'adt.php?id=' . $id_adt);
    $doc->ret(__('Реклама и баннеры'), 'adt.php');
    $doc->ret(__('Админка'), '/dpanel/');
    $doc->err(__('Рекламная позиция не найдена'));
    exit;
}

$adt = mysql_fetch_assoc($q);

echo __('Название') . ": " . for_value($adt['name']) . "<br />\n";

if ($adt['time_create']) {
    echo __('Позиция создана: %s', vremja($adt['time_create'])) . "<br />\n";
}

if (!$adt['time_start']) {
    echo __('Начало показа неизвестно') . "<br />\n";
} elseif ($adt['time_start'] > TIME) {
    echo "<b>" . __('Начало показа') . "</b>: " . vremja($adt['time_start']) . "<br />\n";
} else {
    echo __("Показ начался: %s", vremja($adt['time_start'])) . "<br />\n";
}

if (!$adt['time_end'])
    echo __('Конец показа не обозначен') . "<br />\n";
elseif ($adt['time_end'] > TIME)
    echo __("Конец показа: %s", vremja($adt['time_end'])) . "<br />\n";
else
    echo __("<b>Показ истек</b>: %s", vremja($adt['time_end'])) . "<br />\n";

echo __("Показы (с времени запуска)") . "<br />\n";
echo "WAP: $adt[count_show_wap]<br />\n";
echo "PDA: $adt[count_show_pda]<br />\n";
echo "iTouch: $adt[count_show_itouch]<br />\n";
echo "WEB: $adt[count_show_web]<br />\n";

echo __("Переходы (с времени запуска)") . "<br />\n";
echo "WAP: $adt[count_out_wap]<br />\n";
echo "PDA: $adt[count_out_pda]<br />\n";
echo "iTouch: $adt[count_out_itouch]<br />\n";
echo "WEB: $adt[count_out_web]<br />\n";

$doc->ret(__('Вернуться'), "adt.php?id=$adt[space]");
$doc->ret(__('Рекламные площадки'), 'adt.php');
$doc->ret(__('Админка'), '/dpanel/');
?>
