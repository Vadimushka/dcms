<?php
include_once 'sys/inc/start.php';
$doc = new document();
$doc->title = __('Гости на сайте');

$pages = new pages;
$pages->posts = $dcms->count('guest_online'); // количество постов
$pages->this_page(); // получаем текущую страницу

$q = mysql_query("SELECT * FROM `guest_online` WHERE `conversions` >= '5' ORDER BY `time_start` DESC LIMIT $pages->limit");

$listing = new listing();
while ($ank = mysql_fetch_assoc($q)) {
    $post = $listing->post();
    $post->icon('guest');
    $post->title = __('Гость');
    $post->content = __("Переходов") . ": $ank[conversions]<br />";
    $post->content .= __("Браузер") . ": $ank[browser]<br />";
    $post->content .= __("IP-адрес") . ": " . long2ip($ank['ip_long']);
}
$listing->display(__('Нет гостей'));

$pages->display('?'); // вывод страниц
?>
