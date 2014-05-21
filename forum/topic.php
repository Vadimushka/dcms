<?php

include_once '../sys/inc/start.php';
$doc = new document();

$doc->title = __('Форум');
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Refresh: 1; url=./');
    $doc->err(__('Ошибка выбора раздела'));

    exit;
}
$id_top = (int) $_GET['id'];

$q = $db->prepare("SELECT `forum_topics`.*, `forum_categories`.`name` AS `category_name` FROM `forum_topics`
LEFT JOIN `forum_categories` ON `forum_categories`.`id` = `forum_topics`.`id_category`
WHERE `forum_topics`.`id` = ? AND `forum_topics`.`group_show` <= ? AND `forum_categories`.`group_show` <= ?");
$q->execute(Array($id_top, $user->group, $user->group));
if (!$topic = $q->fetch()) {
    header('Refresh: 1; url=./');
    $doc->err(__('Раздел не доступен'));

    exit;
}



$doc->title .= ' - ' . $topic['name'];

$res = $db->prepare("SELECT COUNT(*) AS cnt FROM `forum_themes` WHERE `id_topic` = ? AND `group_show` <= ?");
$res->execute(Array($topic['id'], $user->group));
$posts = array();
$pages = new pages;
$pages->posts = ($row = $res->fetch()) ? $row['cnt'] : 0; // количество категорий форума
$pages->this_page(); // получаем текущую страницу

$q = $db->prepare("SELECT `forum_themes`.* ,
        COUNT(`forum_messages`.`id`) AS `count`,
        (SELECT COUNT(`fv`.`id_user`) FROM `forum_views` AS `fv` WHERE `fv`.`id_theme` = `forum_themes`.`id`)  AS `views`
        FROM `forum_themes`
LEFT JOIN `forum_messages` ON `forum_messages`.`id_theme` = `forum_themes`.`id`

 WHERE `forum_themes`.`id_topic` = ? AND `forum_themes`.`group_show` <= ? AND `forum_messages`.`group_show` <= ?
 GROUP BY `forum_themes`.`id`
 ORDER BY `forum_themes`.`top`, `forum_themes`.`time_last` DESC LIMIT $pages->limit");
$q->execute(Array($topic['id'], $user->group, $user->group));
$listing = new listing();

if ($arr = $q->fetchAll()) {
    foreach ($arr AS $themes) {
        $post = $listing->post();

        $is_open = (int) ($themes['group_write'] <= $topic['group_write']);

    $post->icon("forum.theme.{$themes['top']}.$is_open");
    $post->title = text::toValue($themes['name']);
    $post->url = 'theme.php?id=' . $themes['id'];
    $post->counter = $themes['count'];
    $post->time = misc::when($themes['time_last']);


        $autor = new user($themes['id_autor']);
        $last_msg = new user($themes['id_last']);

        $post->content = ($autor->id != $last_msg->id ? $autor->nick . '/' . $last_msg->nick : $autor->nick) . '<br />';
        $post->content .= __('Просмотров: %s', $themes['views']);
    }
}


$listing->display(__('Доступных Вам тем нет'));

$pages->display('topic.php?id=' . $topic['id'] . '&amp;'); // вывод страниц

if ($topic['group_write'] <= $user->group) {
    $doc->act(__('Начать новую тему'), 'theme.new.php?id_topic=' . $topic['id'] . "&amp;return=" . URL);
}

if ($topic['group_edit'] <= $user->group) {
    $doc->act(__('Параметры раздела'), 'topic.edit.php?id=' . $topic['id'] . "&amp;return=" . URL);
}

$doc->ret($topic['category_name'], 'category.php?id=' . $topic['id_category']);
$doc->ret(__('Форум'), './');
?>