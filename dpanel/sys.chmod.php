<?php

include_once '../sys/inc/start.php';
//dpanel::check_access();
$doc = new document(5);
$doc->title = __('Проверка CHMOD');

$nw = ini::read(H . '/sys/ini/chmod.ini');

$listing = new listing();


$err = array();
foreach ($nw as $path) {
    $e = check_sys::getChmodErr($path, true);

    $post = $listing->post();
    $post->icon($e ? 'error' : 'checked');
    $post->title = $path;


    //echo $path . ' ' . ($e ? '<span style="font-weight:bold">[' . __('Проблема') . ']</span>' : '[' . __('OK') . ']') . '<br />';

    $err = array_merge($err, $e);
}

$listing->display();


if ($err) {
    echo '<textarea>';
    foreach ($err as $error) {
        echo $error . "\r\n";
    }

    echo '</textarea><br />';
    echo '* ' . __('В зависимости от настроек на хостинге, CHMOD для возможности записи должен быть от 644 до 666') . '<br />';
}else
    $doc->msg( __('Необходимые права на запись имеются'));

$doc->ret(__('Админка'), '/dpanel/');
?>
