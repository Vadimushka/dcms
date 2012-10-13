<?php

include_once '../sys/inc/start.php';
$doc = new document();
$doc->title = 'WWW - домены';
$isp = new ispmanager();
include_once 'inc/check_login.php';
$doc->ret('Панель управления', 'index.php');
$list = $isp->getData('wwwdomain');


$listing = new listing();
foreach ($list->elem as $elem) {
    $post = $listing->post();
    $post->icon('www');
    $post->url = 'wwwdomain.edit.php?elid=' . urlencode($elem->name);
    $post->title = $elem->name;
    $post->content[] = '[b]IP:[/b] ' . $elem->ip;
    $post->content[] = '[b]Путь:[/b] ' . $elem->docroot;
    $post->content[] = '[b]Режим PHP:[/b] ' . $elem->php;
    $post->action('delete', 'wwwdomain.delete.php?elid=' . urlencode($elem->name));
}
$listing->display('Список WWW - доменов пуст');


//echo '<pre>' . print_r($list, true) . '</pre>';
$doc->act('Создать WWW - домен', 'wwwdomain.create.php');
?>
