<?php
include_once '../sys/inc/start.php';
$doc = new document(5);
$name = $_GET['name'];
$doc->title = 'Удаление страницы - '.$name;

if(isset($_GET['del'])){
    if(isset($_POST['no']))
    {
        header("Location: files.php?name".$name);
        exit;
    }
    if(isset($_POST['yes']))
    {
        $path = 'files/'.$_GET['name'].'.txt';
        if(file_exists($path))
        {
            unlink($path);
            $doc->msg('Успешно');
            header("Location: add.pages.php");
        }else{
            $doc->err('Ошибка');
        }
    }
}

$form = new form('?del&amp;name='.$name);
$form->bbcode(__('Вы действительно хотите удалить страницу??'));
$form->button(__('Нет'), 'no', false);
$form->button(__('Да'), 'yes');
$form->display();
?>