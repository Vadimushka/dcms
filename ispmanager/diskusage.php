<?php

include_once '../sys/inc/start.php';
$doc = new document();
$doc->title = 'Использование диска';
$isp = new ispmanager();
include_once 'inc/check_login.php';
$doc->ret('Панель управления', 'index.php');

$elid = isset($_GET['elid']) ? $_GET['elid'] : '.';

$list = $isp->getData('diskusage', array('elid' => $elid));

$listing = new listing();

if ($list->plid) {
    $post = $listing->post();
    $post->icon('up');
    $post->url = '?elid=' . urlencode(dirname($list->plid));
    $post->title = 'Выше';
}


foreach ($list->elem as $elem) {
    $post = $listing->post();
    $post->icon('folder');
    $post->url = '?elid=' . urlencode($elid . '/' . $elem->name);
    $post->title = $elem->name;
    $post->content[] = '[b]Размер:[/b] ' . size_data($elem->size * 1048576);
}
$listing->display();

//echo '<pre>' . print_r($list, true) . '</pre>';
?>
