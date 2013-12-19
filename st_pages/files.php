<?php
include '../sys/inc/start.php';
$doc = new document();
$doc->title = 'Просмотр страницы';

$files = array();
$files_g = (array) glob(H . '/st_pages/files/*.txt');
foreach ($files_g as $path) {
    if (preg_match("#([^/]*?)\.txt#", $path, $m)) {
        $files[] = $m[1];
    }
}

$files = array_reverse($files);

if (!empty($_GET['name'])) {
    if (!in_array($_GET['name'], $files)) {
        $doc->err(__('Страница не найдена, возможно она была удалена с сервера'));
    } else {
        $fname = basename($_GET['name']);
        $bb = new bb(H . '/st_pages/files/' . $fname . '.txt');
        if ($bb->title) {
            $doc->title = $bb->title;
        }
		
		echo '<div style="margin-bottom: 10px; box-shadow: 0px 1px 17px -9px black;">';
		$bb->display();
		echo '</div>';
		
		if($user->group >= 5){
        $doc->act(__('Редактировать страницу'), 'edit.page.file.php?name='.$_GET['name']);
        $doc->act(__('Удалить страницу'), 'del.pages.file.php?name='.$_GET['name']);
		}
        $doc->ret(__('Главная'), '/');
        exit;
    }
}
?>