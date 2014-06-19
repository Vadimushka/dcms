<?php

include_once '../sys/inc/start.php';

if (AJAX)
    $doc = new document_json(1);
else
    $doc = new document(1);

$doc->title = __('Моя почта');

if (isset($_GET ['id'])) {
    $id_kont = (int)$_GET ['id'];
    $ank = new user($id_kont);
    if (!$ank->group && !mysql_result(mysql_query("SELECT COUNT(*) FROM `mail` WHERE `id_user` = '{$user->id}' AND `id_sender` = '$id_kont'"), 0)) {
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

    if ($accept_send && isset($_POST ['post']) && isset($_POST ['mess']) && isset($_POST ['token'])) {
        $mess = (string)$_POST ['mess'];
        text::nickSearch($mess); // поиск и преобразование @nick
        $mess = text::input_text($mess);

        if (!antiflood::useToken($_POST['token'], 'mail')) {

        } elseif (!$mess)
            $doc->err(__('Сообщение пусто'));
        else {
            $ank->mess($mess, $user->id);

            if ($doc instanceof document_json) {
                $doc->form_value('mess', '');
                $form->hidden('token', antiflood::getToken('mail'));
            }


            $doc->msg(__('Сообщение успешно отправлено'));
            header('Refresh: 1; url=?id=' . $id_kont);
            exit();
        }
        if ($doc instanceof document_json)
            $form->hidden('token', antiflood::getToken('mail'));
        $doc->ret(__('К сообщениям'), '?id=' . $id_kont);
    }

    $doc->title = __('Переписка с "%s"', $ank->login);

    if ($accept_send) {
        $form = new form("/my.mail.php?id=$id_kont&amp;" . passgen());
        $form->textarea('mess', __('Сообщение'));
        $form->hidden('token', antiflood::getToken('mail'));
        $form->button(__('Отправить'), 'post', false);
        $form->refresh_url("/my.mail.php?id=$id_kont&amp;" . passgen());
        $form->setAjaxUrl('/my.mail.php?id=' . $id_kont);
        $form->display();
    }


    $pages = new pages ();
    $pages->posts = mysql_result(mysql_query("SELECT COUNT(*) FROM `mail` WHERE (`id_user` = '{$user->id}' AND `id_sender` = '$id_kont') OR (`id_user` = '$id_kont' AND `id_sender` = '{$user->id}')"), 0); // количество писем
    //$pages->this_page(); // получаем текущую страницу


    $q = mysql_query("SELECT * FROM `mail`
WHERE (`id_user` = '{$user->id}' AND `id_sender` = '$id_kont')
      OR (`id_user` = '$id_kont' AND `id_sender` = '{$user->id}')
ORDER BY `id` DESC
LIMIT " . $pages->limit);
    // отметка о прочтении писем
    mysql_query("UPDATE `mail` SET `is_read` = '1' WHERE `id_user` = '{$user->id}' AND `id_sender` = '$id_kont'");
    if (preg_match('#Changed: ([0-9]+)#i', mysql_info(), $ch)) {
        if ($ch [1]) {
            $user->mail_new_count = $user->mail_new_count - $ch [1];
        }
    }

    $id_after = false;
    $listing = new listing();
    while ($mail = mysql_fetch_assoc($q)) {
        $ank2 = new user((int)$mail ['id_sender']);
        $post = $listing->post();
        $post->id = 'chat_' . $mail['id'];
        $post->title = $ank2->nick();
        $post->url = '/profile.view.php?id=' . $ank2->id;
        $post->icon($ank2->icon());
        $post->content = text::toOutput($mail ['mess']);
        $post->hightlight = !$mail ['is_read'];
        $post->time = misc::when($mail ['time']);

        if ($doc instanceof document_json)
            $doc->add_post($post, $id_after);

        $id_after = $post->id;
    }
    if (isset($form))
        $listing->setForm($form);
    $listing->setAjaxUrl('?id=' . $ank->id);

    $listing->display(__('Переписка отсутствует'));

    if ($doc instanceof document_json)
        $doc->set_pages($pages);

    $pages->display('?id=' . $ank->id . '&amp;'); // вывод страниц

    $doc->ret(__('Все сообщения'), '/my.mail.php');
    $doc->ret(__('Личное меню'), '/menu.user.php');
    exit();
}

$user->mail_new_count = mysql_result(mysql_query("SELECT COUNT(*) FROM `mail` WHERE `id_user` = '$user->id' AND `is_read` = '0'"), 0);

$sql_where = array("`mail`.`id_user` = '{$user->id}'");
if (isset($_GET ['only_unreaded'])) {
    $sql_where[] = "`mail`.`is_read` = '0'";
}

$pages = new pages ();
$pages->posts = mysql_result(mysql_query("SELECT COUNT(DISTINCT(`mail`.`id_sender`)) FROM `mail` WHERE " . implode(' AND ', $sql_where)), 0); // количество написавших пользователей
//$pages->this_page(); // получаем текущую страницу

$q = mysql_query("SELECT `users`.`id`,
        `mail`.`id_sender`,
        MAX(`mail`.`time`) AS `time`,
        MIN(`mail`.`is_read`) AS `is_read`,
        COUNT(`mail`.`id`) AS `count`
FROM `mail`
LEFT JOIN `users` ON `mail`.`id_sender` = `users`.`id`
WHERE " . implode(' AND ', $sql_where) . "
GROUP BY `mail`.`id_sender`
ORDER BY `time` DESC
LIMIT $pages->limit");

$listing = new listing();
while ($mail = mysql_fetch_assoc($q)) {
    $ank = new user((int)$mail['id_sender']);
    $post = $listing->post();
    $post->icon($ank->icon());
    $post->url = '?id=' . $ank->id;
    $post->title = $ank->nick();
    $post->counter = isset($_GET ['only_unreaded']) ? '+' . $mail['count'] : $mail['count'];
    $post->hightlight = !$mail['is_read'];
}

$listing->display(__('Почта отсутствует'));

$pages->display('?');
$doc->ret(__('Личное меню'), '/menu.user.php');