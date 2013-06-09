<?php

include_once 'sys/inc/start.php';
$doc = new document();
$pages = new pages;
$res = $db->query("SELECT COUNT(*) AS cnt FROM `guest_online` WHERE `conversions` >= '5'");
$pages->posts = ($row = $res->fetch()) ? $row['cnt'] : 0;
$pages->this_page(); // получаем текущую страницу

$doc->title = __('Гости на сайте (%s)', $pages->posts);

$q = $db->query("SELECT * FROM `guest_online` WHERE `conversions` >= '5' ORDER BY `time_start` DESC LIMIT $pages->limit");

$listing = new listing();
while ($ank = $q->fetch()) {
    $post = $listing->post();
    $post->icon('guest');
    $post->title = __('Гость');
    $post->content = __("Переходов") . ': ' . $ank['conversions'] . '<br />';
    $post->content .= __("Браузер") . ': ' . $ank['browser'] . '<br />';
    $post->content .= __("IP-адрес") . ": " . long2ip($ank['ip_long']);
}
$listing->display(__('Нет гостей'));

$pages->display('?'); // вывод страниц
?>
