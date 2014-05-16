<?php

include_once '../sys/inc/start.php';
$doc = new document(1);
$doc->title = __('Моя почта');

if (isset($_GET ['id'])) {
    $id_kont = (int)$_GET ['id'];
    $ank = new user($id_kont);
    $res = $db->prepare("SELECT COUNT(*) AS cnt FROM `mail` WHERE `id_user` = ? AND `id_sender` = ?");
    $res->execute(Array($user->id, $id_kont));
    if (!$ank->group && !$res->fetch()) {
        $doc->err(__('Пользователь не найден'));
        $doc->ret(__('К почте'), '?');
        exit;
    }

    $can_write = true;
    if (!$user->is_writeable) {
        $doc->msg(__('Писать запрещено'), 'write_denied');
        $can_write = false;
    }

    $accept_send = $ank->id && $ank->group && $ank->id != $user->id && $can_write;

    if ($ank->mail_only_friends && !$ank->is_friend($user)) {
        $accept_send = false;
        $doc->err(__('Писать сообщения могут только друзья'));
        if ($user->group > $ank->group) {
            $accept_send = true;
            $doc->msg(__('Ваш статус позволяет оставить сообщение данному пользователю несмотря на установленное ограничение'));
        }
    } elseif ($ank->id && $user->mail_only_friends && !$user->is_friend($ank) && $user->group >= $ank->group) {
        $doc->err(__('Пользователь не сможет Вам ответить'));
    }

    if ($accept_send && isset($_POST ['post']) && isset($_POST ['mess'])) {
        $mess = (string)$_POST ['mess'];
        text::nickSearch($mess); // поиск и преобразование @nick
        $mess = text::input_text($mess);

        if ($user->group <= $ank->group && !$ank->is_friend($user) && (empty($_POST ['captcha']) || empty($_POST ['captcha_session']) || !captcha::check($_POST ['captcha'], $_POST ['captcha_session'])))
            $doc->err(__('Проверочное число введено неверно'));
        elseif (!$mess)
            $doc->err(__('Сообщение пусто')); else {
            $ank->mess($mess, $user->id);
            $doc->msg(__('Сообщение успешно отправлено'));
            header('Refresh: 1; url=?id=' . $id_kont);
            exit();
        }

        $doc->ret(__('К сообщениям'), '?id=' . $id_kont);
    }

    $doc->title = __('Переписка с "%s"', $ank->login);

    if ($accept_send) {
        $form = new form("/my.mail.php?id=$id_kont&amp;" . passgen());
        $form->textarea('mess', __('Сообщение'));

        if ($user->group <= $ank->group && !$ank->is_friend($user))
            $form->captcha();

        $form->button(__('Отправить'), 'post', false);
        $form->refresh_url("/my.mail.php?id=$id_kont&amp;" . passgen());
        //$form ->button(__('Обновить'), 'refresh', false);
        $form->display();
    }


    $pages = new pages ();
    $res = $db->prepare("SELECT COUNT(*) AS cnt FROM `mail` WHERE (`id_user` = ? AND `id_sender` = ?) OR (`id_user` = ? AND `id_sender` = ?)");
    $res->execute(Array($user->id, $id_kont, $id_kont, $user->id));
    $pages->posts = ($row = $res->fetch()) ? $row['cnt'] : 0; // количество писем


    $q = $db->prepare("SELECT * FROM `mail`
WHERE (`id_user` = ? AND `id_sender` = ?)
      OR (`id_user` = ? AND `id_sender` = ?)
ORDER BY `id` DESC
LIMIT " . $pages->limit);
    $q->execute(Array($user->id, $id_kont, $id_kont, $user->id));

    // отметка о прочтении писем
    $res = $db->prepare("UPDATE `mail` SET `is_read` = '1' WHERE `id_user` = ? AND `id_sender` = ?");
    $res->execute(Array($user->id, $id_kont));

    $user->mail_new_count = $user->mail_new_count - $res->rowCount();
    $listing = new listing();

    if ($arr = $q->fetchAll()) {
        foreach ($arr AS $mail) {
            $ank2 = new user((int)$mail ['id_sender']);
            $post = $listing->post();
            $post->title = $ank2->nick();
            $post->url = '/profile.view.php?id=' . $ank2->id;
            $post->icon($ank2->icon());
            $post->content = text::toOutput($mail ['mess']);
            $post->hightlight = !$mail ['is_read'];
            $post->time = misc::when($mail ['time']);
        }

    }
    $listing->display(__('Переписка отсутствует'));

    $pages->display('?id=' . $ank->id . '&amp;'); // вывод страниц


    $doc->ret(__('Все сообщения'), '/my.mail.php');
    $doc->ret(__('Личное меню'), '/menu.user.php');
    exit();
}

$res = $db->prepare("SELECT COUNT(*) AS cnt FROM `mail` WHERE `id_user` = ? AND `is_read` = '0'");
$res->execute(Array($user->id));
$user->mail_new_count = ($row = $res->fetch()) ? $row['cnt'] : 0;

$pages = new pages ();
if (isset($_GET ['only_unreaded'])) {
    $res = $db->prepare("SELECT COUNT(DISTINCT(`mail`.`id_sender`)) AS cnt FROM `mail` WHERE `mail`.`id_user` = ? AND `mail`.`is_read` = '0'");
    $q = $db->prepare("SELECT `users`.`id`,
        `mail`.`id_sender`,
        MAX(`mail`.`time`) AS `time`,
        MIN(`mail`.`is_read`) AS `is_read`,
        COUNT(`mail`.`id`) AS `count`
        FROM `mail`
        LEFT JOIN `users` ON `mail`.`id_sender` = `users`.`id`
        WHERE `mail`.`id_user` = ? AND `mail`.`is_read` = '0'
        GROUP BY `mail`.`id_sender`
        ORDER BY `time` DESC
        LIMIT " . $pages->limit);
} else {

    $res = $db->prepare("SELECT COUNT(DISTINCT(`mail`.`id_sender`)) AS cnt FROM `mail` WHERE `mail`.`id_user` = ?");
    $q = $db->prepare("SELECT `users`.`id`,
        `mail`.`id_sender`,
        MAX(`mail`.`time`) AS `time`,
        MIN(`mail`.`is_read`) AS `is_read`,
        COUNT(`mail`.`id`) AS `count`
        FROM `mail`
        LEFT JOIN `users` ON `mail`.`id_sender` = `users`.`id`
        WHERE `mail`.`id_user` = ?
        GROUP BY `mail`.`id_sender`
        ORDER BY `time` DESC
        LIMIT " . $pages->limit);
}


$res->execute(Array($user->id));
$pages->posts = ($row = $res->fetch()) ? $row['cnt'] : 0; // количество написавших пользователей
$q->execute(Array($user->id));
$listing = new listing();
if ($arr = $q->fetchAll()) {
    foreach ($arr AS $mail) {
        $ank = new user((int)$mail['id_sender']);
        $post = $listing->post();
        $post->icon($ank->icon());
        $post->url = '?id=' . $ank->id;
        $post->title = $ank->nick();
        $post->counter = isset($_GET ['only_unreaded']) ? '+' . $mail['count'] : $mail['count'];
        $post->hightlight = !$mail['is_read'];
    }
}


$listing->display(__('Почта отсутствует'));

$pages->display('?');
$doc->ret(__('Личное меню'), '/menu.user.php');