<?php

include_once '../sys/inc/start.php';
$doc = new document();

$pages = new pages(mysql_result(mysql_query("SELECT COUNT(*) FROM `users_online`"), 0)); // получаем текущую страницу

$doc->title = __('Сейчас на сайте (%s)', $pages->posts);

$q = mysql_query("SELECT `users_online`.* , `browsers`.`name` AS `browser`
 FROM `users_online`
 LEFT JOIN `browsers`
 ON `users_online`.`id_browser` = `browsers`.`id`
 ORDER BY `users_online`.`time_login` DESC LIMIT " . $pages->limit);


$listing = new listing();
while ($ank = mysql_fetch_assoc($q)) {
    $p_user = new user($ank['id_user']);
    $post = $listing->post();
    $post->title = $p_user->nick();
    $post->url = '/profile.view.php?id=' . $p_user->id;
    $post->icon($p_user->icon());


    if ($user->group) {
        $post->content .= __('Браузер') . ': ' . text::toValue($ank['browser']) . "<br />\n";
        if ($user->id === $p_user->id || $user->group > $p_user->group)
            $post->content .= __('IP-адрес') . ': ' . long2ip($ank['ip_long']) . "<br />\n";
        $post->content .= __('Переходов') . ': ' . $ank['conversions'] . "<br />";
        $post->content .= __('Последний визит') . ': ' . misc::when($p_user->last_visit) . '<br />';
    }
}

$listing->display(__('Нет пользователей'));

$pages->display('?');