<?php

include_once '../sys/inc/start.php';
//dpanel::check_access();
$doc = new document(5);
$doc->title = __('Информация о системе');

echo __('Версия DCMS: %s', $dcms->version) . "<br />";



$check = new check_sys();

foreach ($check->oks as $ok) {
    echo "$ok<br />";
}

foreach ($check->errors as $err) {
    echo "<span style='font-weight:bold'>" . __('Ошибка') . ": $err</span><br />";
}

foreach ($check->notices as $note) {
    echo "<span style='font-weight:bold'>" . __('Примечание') . ":</span> $note<br />";
}

$doc->ret(__('Админка'), '/dpanel/');
?>
