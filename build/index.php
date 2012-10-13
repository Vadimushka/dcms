<?php

include '../sys/inc/start.php';
$doc = new document();
$doc->title = 'Скачать DCMS';

$bb = new bb('description.txt');
$bb->display();


$conf = ini::read('config.ini');

$ch_files = (array) glob(H . '/sys/docs/changelog/' . $conf['version_last'] . '.*.txt');

foreach ($ch_files as $ch_file) {
    echo '<b>' . basename($ch_file, '.txt') . "</b>:<br />";

    $bb = new bb($ch_file);
    $bb->display();
    echo "<br />";
}


$info = ini::read('builds/' . $conf['version_last'] . '.' . $conf['build_num'] . '.ini');

if (empty($info['dcount'])) {
    $info['dcount'] = 0;
}

echo __("Кол-во скачиваний") . ": " . $info['dcount'] . ' ' . __(misc::number($info['dcount'], 'раз', 'раза', 'раз')) . "<br />\n";



$smarty = new design();
$smarty->assign('method', 'get');
$smarty->assign('action', 'download/' . $conf['version_last'] . '.' . $conf['build_num'] . '.zip');
$elements = array();
$elements[] = array('type' => 'submit', 'br'   => 0, 'info' => array('value' => __('Скачать %s', 'DCMS ' . $conf['version_last']))); // кнопка
$smarty->assign('el', $elements);
$smarty->display('input.form.tpl');
?>