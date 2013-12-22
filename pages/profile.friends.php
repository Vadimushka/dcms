<?php

include_once '../sys/inc/start.php';
$doc = new document(1);
$doc->title = __('Друзья');

$ank = new user(@$_GET['id']);

if (!$ank->id) {
    if (isset($_GET['return'])) {
        header('Refresh: 1; url=' . $_GET['return']);
    } else {
        header('Refresh: 1; url=/');
    }
    $doc->err(__('Нет данных'));
    exit;
}

if (!$ank->is_friend($user) && !$ank->vis_friends) {
    if (isset($_GET['return'])) {
        header('Refresh: 1; url=' . $_GET['return']);
    } else {
        header('Refresh: 1; url=/');
    }
    $doc->err(__('Доступ к данной странице ограничен'));
    exit;
}
$doc->title = __('Друзья %s', $ank->login);

$posts = array();

$pages = new pages;

$res = $db->prepare("SELECT COUNT(*) AS cnt FROM `friends` WHERE `id_user` = ? AND `confirm` = '1'");
$res->execute(Array($ank->id));
$pages->posts = ($row = $res->fetch()) ? $row['cnt'] : 0; // количество друзей
$pages->this_page(); // получаем текущую страницу

$q = $db->prepare("SELECT * FROM `friends` WHERE `id_user` = ? AND `confirm` = '1' ORDER BY `time` DESC LIMIT $pages->limit");
$q->execute(Array($ank->id));


$listing = new listing();
while ($arr = $q->fetchAll()) {
    foreach ($arr AS $friend) {
        $fr = new user($friend['id_friend']);
        $post = $listing->post();
        $post->title = $fr->nick();
        $post->url = '/profile.view.php?id=' . $fr->id;
        $post->icon($fr->icon());
    }
}
$listing->display(__('У пользователя "%s" еще нет друзей', $ank->login));

$pages->display('?id=' . $ank->id . '&amp;'); // вывод страниц

$doc->ret(__('Анкета "%s"', $ank->login), '/profile.view.php?id=' . $ank->id);
?>