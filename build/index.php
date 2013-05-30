<?php

include '../sys/inc/start.php';
$doc = new document();
$doc->title = 'Скачать DCMS';

$listing = new listing();

$post = $listing->post();
$post->title = __('Описание');
$post->icon('info');
$bb = new bb('description.txt');
$post->content = $bb->fetch();


$conf = ini::read('config.ini');

$ch_files = (array) glob(H . '/sys/docs/changelog/' . $conf['version_last'] . '.*.txt');


$post = $listing->post();
$post->title = __('Список изменений');
$post->icon('changelog');

foreach ($ch_files as $ch_file) {
    $ch_ver = basename($ch_file, '.txt');

    if (version_compare($ch_ver, $conf['version_last'] . '.' . $conf['build_num'], '>'))
        continue;

    $post->content[] = '[b]' . $ch_ver . "[/b]";
    $bb = new bb($ch_file);
    $post->content[] = trim($bb->getText());
}


$info = ini::read('builds/' . $conf['version_last'] . '.' . $conf['build_num'] . '.ini');

if (empty($info['dcount'])) {
    $info['dcount'] = 0;
}


$post = $listing->post();
$post->title = __('Кол-во скачиваний');
$post->content = $info['dcount'] . ' ' . __(misc::number($info['dcount'], 'раз', 'раза', 'раз'));

//echo __("Кол-во скачиваний") . ": " . $info['dcount'] . ' ' . __(misc::number($info['dcount'], 'раз', 'раза', 'раз')) . "<br />\n";

$listing->display();


$form = new form('download/' . $conf['version_last'] . '.' . $conf['build_num'] . '.zip', false);
$form->button(__('Скачать %s', 'DCMS ' . $conf['version_last']. '.' . $conf['build_num']));
$form->display();
?>