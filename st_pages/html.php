<?php
include_once '../sys/inc/start.php';
$doc = new document();
$doc->title = $_GET['name'];
if(!$_GET['name'])
{
	$doc->err('Не введён параметр name');
	exit;
}

$name = $_GET['name'];

if(!file_exists('html/'.$name))
{
	$doc->err('Такого файла не существует');
	exit;
}

$content = file_get_contents('html/'.$name);

$listing = new listing();
$post = $listing->post();
$post->content = $content;
$post->display();

if($user->group >= 5)
	$doc->ret(__('Редактировать'), 'edit.html.php?name='.$name);
?>