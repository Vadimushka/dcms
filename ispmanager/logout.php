<?php
include_once '../sys/inc/start.php';
$doc = new document();
$doc->title = 'Выход - Панель управления';
$isp = new ispmanager();

if (!$isp->isAutorized()) {
    $doc->msg('Вы не авторизованы');
    $listing = new listing();
    $post = $listing->post();
    $post->url = 'auth.php';
    $post->title = 'Авторизация';
    $listing->display();
    exit;
}


$isp ->sessionReset();
$doc->msg('Авторизация успешно сброшена');
?>
