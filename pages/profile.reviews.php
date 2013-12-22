<?php

include_once 'sys/inc/start.php';
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

$q = $db->prepare("SELECT COUNT(*) as `count`, MAX(`time`) as `time` FROM `reviews_users` WHERE `id_user` = ? AND `id_ank` = ?");
$q->execute(Array($user->id, $ank->id));
if ($row = $q->fetch()) {
    $count = $row['count'];
    $time = $row['time'];
} else {
    $count = 0;
    $time = 0;
}



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
        $res = $db->prepare("UPDATE `users` SET `rating` = `rating` + ? WHERE `id` = ? LIMIT 1");
        $res->execute(Array($add, $ank->id));
        $res = $db->prepare("INSERT INTO `reviews_users` (`id_user`, `id_ank`, `time`, `text`, `rating`) VALUES (?, ?, ?, ?, ?)");
        $res->execute(Array($user->id, $ank->id, TIME, $message, $add));
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
$res = $db->prepare("SELECT COUNT(*) AS cnt FROM `reviews_users` WHERE `id_ank` = ?");
$res->execute(Array($ank->id));
$pages->posts = ($row = $res->fetch()) ? $row['cnt'] : 0; // количество сообщений
$pages->this_page(); // получаем текущую страницу

$q = $db->prepare("SELECT * FROM `reviews_users` WHERE `id_ank` = ? ORDER BY `id` DESC LIMIT $pages->limit");
$q->execute(Array($ank->id));

$listing = new listing();
if ($arr = $q->fetchAll()) {
    foreach ($arr AS $rev) {

        $ank2 = new user($rev['id_user']);
        $post = $listing->post();
        $post->title = $ank2->nick();
        $post->counter = '+' . $rev['rating'];
        $post->icon($ank2->icon());
        $post->content = text::toOutput($rev['text']);
    }

}
$listing->display(__('Отзывы отсутствуют'));

$pages->display('?id=' . $ank->id . '&amp;'); // вывод страниц

if ($user->group && $can_write && $user->id != $ank->id && $add) {
    $smarty = new design();
    $smarty->assign('method', 'post');
    $smarty->assign('action', '?id=' . $ank->id . '&amp;' . passgen());
    $elements = array();
    $elements[] = array('type' => 'textarea', 'title' => __('Отзыв о пользователе') . ' *', 'br' => 1, 'info' => array('name' => 'review'));
    $elements[] = array('type' => 'text', 'value' => '* ' . __('Разрешается оставлять только положительные отзывы. Кроме того каждый отзыв увеличивает пользователю рейтинг.'), 'br' => 1);
    $elements[] = array('type' => 'submit', 'br' => 0, 'info' => array('value' => __('Отправить'))); // кнопка

    $smarty->assign('el', $elements);
    $smarty->display('input.form.tpl');
}

$doc->ret(__('В анкету'), "profile.view.php?id={$ank->id}");
$doc->ret(__('Личное меню'), '/menu.user.php');
?>