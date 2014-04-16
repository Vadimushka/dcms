<?php

include_once '../sys/inc/start.php';
$doc = new document(); // инициализация документа для браузера
$doc->title = __('Отзывы');

if (isset($_GET['id']))
    $ank = new user($_GET['id']);
else
    $ank = $user;

if (!$ank->group) {
    $doc->access_denied(__('Нет данных'));
}

$can_write = true;
if (!$user->is_writeable) {
    $doc->msg(__('Писать запрещено'), 'write_denied');
    $can_write = false;
}


$add = 1;

$q = mysql_query("SELECT COUNT(*) AS `count`, MAX(`time`) AS `time` FROM `reviews_users` WHERE `id_user` = '$user->id' AND `id_ank` = '$ank->id'");

$count = mysql_result($q, 0, 'count');
$time = mysql_result($q, 0, 'time');
// чем больше отзывов оставлено, тем меньше это влияет на рейтинг
$add = 1 - min($count, 9) / 10;
// оставлять отзыв можно не чаще одного раза в сутки
if ($time > NEW_TIME)
    $add = 0;
// VIP пользователю рейтинг засчитывается вдвойне
if ($ank->is_vip)
    $add += $add;

if ($ank->id == $user->id)
    $doc->title = __('Отзывы обо мне');
else
    $doc->title = __('"Отзывы о "%s"', $ank->login);

if ($user->group && $can_write && isset($_POST['review']) && $user->id != $ank->id && $add) {
    $message = text::input_text($_POST['review']);

    if ($message) {
        mysql_query("UPDATE `users` SET `rating` = `rating` + '$add' WHERE `id` = '$ank->id' LIMIT 1");
        mysql_query("INSERT INTO `reviews_users` (`id_user`, `id_ank`, `time`, `text`, `rating`) VALUES ('$user->id', '$ank->id', '" . TIME . "', '" . my_esc($message) . "', '$add')");
        header('Refresh: 1; url=?id=' . $ank->id);
        $doc->ret(__('Вернуться'), '?id=' . $ank->id);
        $doc->msg(__('Ваш отзыв успешно оставлен'));

        $ank->mess("$user->login оставил" . ($user->sex ? '' : 'а') . " о Вас свой [url=/profile.reviews.php]отзыв[/url]");

        exit;
    } else {
        $doc->err(__('Текст отзыва пуст'));
    }
}

$pages = new pages;
$pages->posts = mysql_result(mysql_query("SELECT COUNT(*) FROM `reviews_users` WHERE `id_ank` = '$ank->id'"), 0); // количество сообщений

$q = mysql_query("SELECT * FROM `reviews_users` WHERE `id_ank` = '$ank->id' ORDER BY `id` DESC LIMIT " . $pages->limit);

$listing = new listing();
while ($rev = mysql_fetch_assoc($q)) {
    $ank2 = new user($rev['id_user']);
    $post = $listing->post();
    $post->title = $ank2->nick();
    $post->counter = '+' . $rev['rating'];
    $post->icon($ank2->icon());
    $post->content = text::toOutput($rev['text']);
    $post->url = '/profile.view.php?id=' . $ank2->id;
}
$listing->display(__('Отзывы отсутствуют'));

$pages->display('?id=' . $ank->id . '&amp;'); // вывод страниц

if ($user->group && $can_write && $user->id != $ank->id && $add) {
    $form = new form('?id=' . $ank->id . '&amp;' . passgen());
    $form->textarea('review', __('Отзыв о пользователе') . ' *');
    $form->bbcode('* ' . __('Разрешается оставлять только положительные отзывы. Кроме того каждый отзыв увеличивает пользователю рейтинг.'));
    $form->button(__('Отправить'));
    $form->display();
}

$doc->ret(__('В анкету'), "profile.view.php?id={$ank->id}");
$doc->ret(__('Личное меню'), '/menu.user.php');