<?php

include_once 'sys/inc/start.php';
$doc = new document(1);
$doc->title = __('Мои друзья');

$res = $db->prepare("SELECT COUNT(*) AS cnt FROM `friends` WHERE `id_user` = ? AND `confirm` = '0'");
$res->execute(Array($user->id));
$user->friend_new_count = ($row = $res->fetch()) ? $row['cnt'] : 0;

$pages = new pages;
$res = $db->prepare("SELECT COUNT(*) AS cnt FROM `friends` WHERE `id_user` = ?");
$res->execute(Array($user->id));
$pages->posts = ($row = $res->fetch()) ? $row['cnt'] : 0;
$pages->this_page();

$q = $db->prepare("SELECT * FROM `friends` WHERE `id_user` = ? ORDER BY `confirm` ASC, `time` DESC LIMIT " . $pages->limit . ";");
$q->execute(Array($user->id));

$listing = new listing();
while ($friend = $q->fetch()) {
    $post = $listing->post();
    $ank = new user($friend['id_friend']);
    $post->url = '/profile.view.php?id=' . $ank->id;
    $post->title = $ank->nick();
    $post->icon($ank->icon());
    $post->hightlight = !$friend['confirm'];
    $post->content = $friend['confirm'] ? null : __('Хочет быть Вашим другом');
}
$listing->display(__('Друзей нет'));

$pages->display('?');

$doc->ret(__('Личное меню'), '/menu.user.php');
?>
