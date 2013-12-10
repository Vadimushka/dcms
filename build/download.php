<?php

include '../sys/inc/start.php';

if (empty($_GET['path'])) {
    exit;
}
$name = basename($_GET['path'], '.zip');

$path = 'builds/' . $name . '.zip';
if (file_exists('builds/' . $name . '.zip')) {
    $info = ini::read('builds/' . $name . '.ini');

    if (empty($info['dcount'])) {
        $info['dcount'] = 1;
    } else {
        $info['dcount']++;
    }
    ini::save('builds/' . $name . '.ini', $info);

    $f = new download('dcms-' . $name . '.zip', $path);
    $f->output();
} else {
    $doc = new document();
    $doc->title = $name;
    $doc->err(__('Файл не найден'));
}
?>
