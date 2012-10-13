<?php
include_once '../sys/inc/start.php';
$doc = new document();
$doc->title = 'Домены (DNS)';
$isp = new ispmanager();
include_once 'inc/check_login.php';
$doc->ret('Панель управления', 'index.php');
$list = $isp->getData('domain');

$listing = new listing();
foreach ($list->elem as $elem) {
    $post = $listing->post();
    $post->icon('domain');
    $post->title = $elem->name;
    $post->action('delete', 'domain.delete.php?elid=' . urlencode($elem->name));
}
$listing->display('Список доменов пуст');

//echo '<pre>' . print_r($list, true) . '</pre>';
$doc->act('Добавить домен', 'domain.create.php');
?>
