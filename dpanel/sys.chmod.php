<?php

include_once '../sys/inc/start.php';
//dpanel::check_access();
$doc = new document(5);
$doc->title = __('Проверка CHMOD');

$nw = ini::read(H . '/sys/ini/chmod.ini');

$err = array();
foreach ($nw as $path) {
    $e = check_sys::getChmodErr($path, true);

    echo $path . ' ' . ($e ? '<span style="font-weight:bold">[' . __('Проблема') . ']</span>' : '[' . __('OK') . ']') . '<br />';

    $err = array_merge($err, $e);
}

if ($err) {
    echo '<textarea>';
    foreach ($err as $error) {
        echo $error . "\r\n";
    }

    echo '</textarea><br />';
    echo '* ' . __('В зависимости от настроек на хостинге, CHMOD для возможности записи должен быть от 644 до 666') . '<br />';
}else
    echo '<span style="font-weight:bold">' . __('Необходимые права на запись имеются') . '</span><br />';

$doc->ret(__('Админка'), '/dpanel/');
?>
