<?php

include_once '../sys/inc/start.php';
$doc = new document();
$doc->title = 'FTP - аккаунты';
$isp = new ispmanager();
include_once 'inc/check_login.php';
$doc->ret('Панель управления', 'index.php');
$list = $isp->getData('ftp');

$listing = new listing();
foreach ($list->elem as $elem) {
    $post = $listing->post();
    $post->icon('ftp');
    $post->url = 'ftp.edit.php?elid=' . urlencode($elem->name);
    $post->title = $elem->name;
    $post->content[] = '[b]Директория:[/b] ' . $elem->home;
    if ($elem->note)
        $post->content[] = str_replace ('<br/>', "\r\n", $elem->note);
    $post->action('delete', 'ftp.delete.php?elid=' . urlencode($elem->name));
}
$listing->display();

//echo '<pre>' . print_r($list, true) . '</pre>';


$doc->act('Создать FTP - аккаунт', 'ftp.create.php');
?>
