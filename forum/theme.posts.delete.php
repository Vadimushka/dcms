<?php

include_once '../sys/inc/start.php';
$doc = new document();
$doc->title = __('Удаление сообщений');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Refresh: 1; url=./');
    $doc->err(__('Ошибка выбора темы'));
    exit;
}
$id_theme = (int) $_GET['id'];

$q = $db->prepare("SELECT * FROM `forum_themes` WHERE `id` = ? AND `group_edit` <= ?");
$q->execute(Array($id_theme, $user->group));
if (!$theme = $q->fetch()) {
    header('Refresh: 1; url=./');
    $doc->err(__('Тема не доступна для редактирования'));
    exit;
}


$doc->title .= ' - ' . $theme['name'];

switch (@$_GET['show']) {
    case 'all':$show = 'all';
        break;
    default:$show = 'part';
        break;
}

$delete_posts = array();

foreach ($_POST as $key => $value) {
    if ($value && preg_match('#^post([0-9]+)$#ui', $key, $n))
        $delete_posts[] = "`forum_messages`.`id` = '$n[1]'";
}

if ($delete_posts) {
    if (isset($_POST['delete'])) {
        foreach ($_POST as $key => $value) {
            if ($value && preg_match('#^post([0-9]+)$#ui', $key, $n)) {
                // удаление папок с файлами сообщений
                $dir = new files(FILES . '/.forum/' . $theme['id'] . '/' . $n[1]);
                $dir->delete();
                unset($dir);
            }
        }

        $res = $db->prepare("DELETE FROM `forum_messages`, `forum_history`
USING `forum_messages`
LEFT JOIN `forum_history`
ON `forum_messages`.`id` = `forum_history`.`id_message`
WHERE `forum_messages`.`id_theme` = ? AND (" . implode(' OR ', $delete_posts) . ")");
        $res->execute(Array($theme['id']));
        $dcms->log('Форум', 'Удаление сообщений из темы [url=/forum/theme.php?id=' . $theme['id'] . ']' . $theme['name'] . '[/url]');

        $doc->msg(__('Успешно удалено %d сообщений', count($delete_posts)));
    }

    if (isset($_POST['hide'])) {
        $res = $db->prepare("UPDATE `forum_messages` SET `forum_messages`.`group_show` = '2' WHERE `forum_messages`.`id_theme` = ? AND (" . implode(' OR ', $delete_posts) . ") LIMIT " . count($delete_posts));
        $res->execute(Array($theme['id']));
        $dcms->log('Форум', 'Скрытие сообщений в теме [url=/forum/theme.php?id=' . $theme['id'] . ']' . $theme['name'] . '[/url]');
        $doc->msg(__('Успешно скрыто %d сообщений', count($delete_posts)));
    }
}
// меню сортировки
$ord = array();
$ord[] = array("?id=$theme[id]&amp;show=all", __('Все'), $show == 'all');
$ord[] = array("?id=$theme[id]&amp;show=part", __('Постранично'), $show == 'part');
$or = new design();
$or->assign('order', $ord);
$or->display('design.order.tpl');



$listing = new listing();

if ($show == 'part') {
    $res = $db->prepare("SELECT COUNT(*) AS cnt FROM `forum_messages` WHERE `id_theme` = ? AND `group_show` <= ?");
    $res->execute(Array($theme['id'], $user->group));
    $pages = new pages;
    $pages->posts = ($row = $res->fetch()) ? $row['cnt'] : 0; // количество сообщений  теме
    $pages->this_page(); // получаем текущую страницу
    $q = $db->prepare("SELECT `id`, `id_user`, `message`, `time` FROM `forum_messages`  WHERE `id_theme` = ? AND `group_show` <= ? ORDER BY `id` ASC LIMIT $pages->limit");
} else
    $q = $db->prepare("SELECT `id`, `id_user`, `message`, `time` FROM `forum_messages`  WHERE `id_theme` = ? AND `group_show` <= ? ORDER BY `id` ASC");

$q->execute(Array($theme['id'], $user->group));
if ($arr = $q->fetchAll()) {
    foreach ($arr AS $messages) {
        $ch = $listing->checkbox();

        $ank = new user((int) $messages['id_user']);

        $ch->title = $ank->nick;
        $ch->time = vremja($messages['time']);
        $ch->name = 'post' . $messages['id'];
        $ch->content = text::for_opis($messages['message']);
    }
}

$form = new form('?id=' . $theme['id']);
$form->html($listing->fetch(__('Сообщения отсутствуют')));
$form->button(__('Удалить'), 'delete', false);
$form->button(__('Скрыть'), 'hide', false);
$form->display();

if ($show == 'part')
    $pages->display('?id=' . $theme['id'] . '&amp;show=part&amp;');

$doc->ret(__('Вернуться в тему'), 'theme.php?id=' . $theme['id'] . ($show == 'part' ? '&amp;page=' . $pages->this_page : ''));

$doc->ret(__('В раздел'), 'topic.php?id=' . $theme['id_topic']);
$doc->ret(__('В категорию'), 'category.php?id=' . $theme['id_category']);
$doc->ret(__('Форум'), './');
?>
