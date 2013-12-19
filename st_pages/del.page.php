<?php
include_once '../sys/inc/start.php';
$doc = new document(5);
$doc->title = __('Удаление страницы');

$id = $_GET['id'];

if($id == NULL){
	$doc->err(__('Идентификатор "id" не определён.'));
	exit;
}

if(isset($_POST['yes'])){
	mysql_query("DELETE FROM `pages` WHERE `id` = '$id'");
	
        $dcms->log('Страницы', 'Удаление страницы "' . $name . '"');
        $doc->msg(__('Страница успешно удалена'));
		$doc->ret(__('Создать страницу'), 'add,pages.php');
		exit;
}
if(isset($_POST['no'])){
	header('Refresh: 1; url=index.php?id='.$id);
	exit;
}

$form = new form('?id='.$id);
$form->bbcode(__('Вы уверены что хотите удалить страницу. Псле удаления её нельзя будет вернуть.'));
$form->button(__('Да'), 'yes');
$form->button(__('Нет'), 'no');
$form->display();
?>