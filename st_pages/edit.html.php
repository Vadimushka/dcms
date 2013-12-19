<?php
include_once '../sys/inc/start.php';
$doc = new document(5);
$doc->title = __('Редактирование HTML страницы');

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

if(isset($_POST['save']))
{
    $path = 'html/'.$_GET['name'];
    $file = fopen($path, w);
    fputs($file, $_POST['str']);
    fclose($file);
    $doc->msg('Успешно');
    $doc->act(__('Просмотреть страницу'), 'html.php?name='.$_GET['name']);
}

$content = file_get_contents('html/'.$name);

$form = new form('?name='.$name);
$form->textarea('str', __('Редактирование страницы'), $content);
$form->button(__('Сохранить'), 'save');
$form->display();

$doc->act(__('Удалить'), '?name='.$name.'&amp;del');

if(isset($_GET['del']) && isset($_GET['name']))
{
	$path = 'html/'.$_GET['name'];
        if(file_exists($path))
        {
            unlink($path);
            $doc->msg('Успешно');
            header("Location: add.pages.php");
        }else{
            $doc->err('Ошибка');
        }
}
?>