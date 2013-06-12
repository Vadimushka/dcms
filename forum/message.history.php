<?php

include_once '../sys/inc/start.php';
$doc = new document();
$doc->title = __('История сообщений');
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Refresh: 1; url=./');
    $doc->err(__('Ошибка выбора сообщения'));
    exit;
}
$id_theme = (int) $_GET['id'];

$q = $db->prepare("SELECT * FROM `forum_messages` WHERE `id` = ? AND `group_show` <= ?");
$q->execute(Array($id_theme, $user->group));
if (!$message = $q->fetch()) {
    header('Refresh: 1; url=./');
    $doc->err(__('Сообщение не доступно'));
    exit;
}

$ank2 = new user($message['id_user']);
if ($message['id_user'] != $user->id && $ank2->group >= $user->group) {
    header('Refresh: 1; url=./');
    $doc->err(__('Нет доступа к данной странице'));
    exit;
}

$res = $db->prepare("SELECT COUNT(*) AS cnt FROM `forum_history` WHERE `id_message` = ?");
$res->execute(Array($message['id']));
$listing = new listing();
$pages = new pages;
$pages->posts = ($row = $res->fetch()) ? $row['cnt'] : 0; // количество сообщений  теме
$pages->this_page(); // получаем текущую страницу



$ank = new user($message['id_user']);

$post = $listing->post();
$post->title = $ank->nick();
$post->icon($ank->icon());
$post->content = text::output_text($message['message']);
$post->time = vremja($message['edit_time'] ? $message['edit_time'] : $message['time']);
$post->bottom = __('Текущая версия');

if ($message['edit_id_user']) {
    $post->bottom .= text::output_text(' ([user]' . $message['edit_id_user'] . '[/user])');
}

$q = $db->prepare("SELECT * FROM `forum_history` WHERE `id_message` = ? ORDER BY `id` DESC LIMIT $pages->limit");
$q->execute(Array($message['id']));
if ($arr = $q->fetchAll()) {
    foreach ($arr AS $messages) {
        $post = $listing->post();
        $ank = new user($message['id_user']);
        $post->title = $ank->nick();
        $post->icon($ank->icon());
        $post->content = text::output_text($messages['message']);
        $post->time = vremja($messages['time']);

        if ($message['id_user'] != $messages['id_user']) {
            $post->bottom = text::output_text('[user]' . $messages['id_user'] . '[/user]');
        }
    }
}
$listing->display(__('Сообщения отсутствуют'));


$pages->display('?id=' . $message['id'] . '&amp;' . (isset($_GET['return']) ? 'return=' . urlencode($_GET['return']) . '&amp;' : null)); // вывод страниц

if (isset($_GET['return']))
    $doc->ret(__('В тему'), for_value($_GET['return']));
else
    $doc->ret(__('В тему'), 'theme.php?id=' . $message['id_theme']);
?>