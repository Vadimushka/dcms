<?php

include_once '../sys/inc/start.php';
$doc = new document(2);
//dpanel::check_access();
$doc->title = __('Панель управления DCMS');
$menu = new menu('dpanel'); // загружаем меню dPanel
$menu->display(); // выводим меню dPanel
?>
