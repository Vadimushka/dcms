<?php

include '../sys/inc/start.php';
$doc = new document();
$doc->title = 'Скачать DCMS';

$listing = new listing();

$post = $listing -> post();
$post -> title = __('Описание');
$post -> icon('info');
$bb = new bb('description.txt');
$post -> content = $bb->fetch();


$conf = ini::read('config.ini');

$ch_files = (array) glob(H . '/sys/docs/changelog/' . $conf['version_last'] . '.*.txt');


$post = $listing -> post();
$post -> title = __('Список изменений');
$post -> icon('changelog');

foreach ($ch_files as $ch_file) {
    $post -> content[] = '[b]' . basename($ch_file, '.txt') . "[/b]";
    $bb = new bb($ch_file);
    $post -> content[] = trim($bb->getText());    
}


$info = ini::read('builds/' . $conf['version_last'] . '.' . $conf['build_num'] . '.ini');

if (empty($info['dcount'])) {
    $info['dcount'] = 0;
}


$post = $listing -> post();
$post -> title = __('Кол-во скачиваний');
$post -> content = $info['dcount'] . ' ' . __(misc::number($info['dcount'], 'раз', 'раза', 'раз'));

//echo __("Кол-во скачиваний") . ": " . $info['dcount'] . ' ' . __(misc::number($info['dcount'], 'раз', 'раза', 'раз')) . "<br />\n";

$listing ->display();

$smarty = new design();
$smarty->assign('method', 'get');
$smarty->assign('action', 'download/' . $conf['version_last'] . '.' . $conf['build_num'] . '.zip');
$elements = array();
$elements[] = array('type' => 'submit', 'br'   => 0, 'info' => array('value' => __('Скачать %s', 'DCMS ' . $conf['version_last']))); // кнопка
$smarty->assign('el', $elements);
$smarty->display('input.form.tpl');
?>