<?php

include_once 'sys/inc/start.php';
$doc = new document();
$doc->title = __('Сейчас на сайте');

$pages = new pages;
$res = $db->query("SELECT COUNT(*) AS cnt FROM `users_online`");
$pages->posts = ($row = $res->fetch()) ? $row['cnt'] : 0;
$pages->this_page(); // получаем текущую страницу

$q = $db->query("SELECT `users_online`.* , `browsers`.`name` AS `browser`
 FROM `users_online`
 LEFT JOIN `browsers`
 ON `users_online`.`id_browser` = `browsers`.`id`
 ORDER BY `users_online`.`time_login` DESC LIMIT $pages->limit");


$listing = new listing();
if ($arr = $q->fetchAll()) {
    foreach ($arr AS $ank) {
        $p_user = new user($ank['id_user']);
        $post = $listing->post();
        $post->title = $p_user->nick();
        $post->url = '/profile.view.php?id=' . $p_user->id;
        $post->icon($p_user->icon());


        if ($user->id === $p_user->id || $user->group > $p_user->group) {
            $post->content .= __('Браузер') . ': ' . for_value($ank['browser']) . "<br />\n";
            $post->content .= __('IP-адрес') . ': ' . long2ip($ank['ip_long']) . "<br />\n";
        }

        $post->content .= __('Переходов') . ': ' . $ank['conversions'] . "<br />";
        $post->content .= __('Последний визит') . ': ' . vremja($p_user->last_visit) . '<br />';
    }
}

$listing->display(__('Нет пользователей'));

$pages->display('?');
?>
