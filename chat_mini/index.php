<?php

include_once '../sys/inc/start.php';
$doc = new document();
$doc->title = __('Мини чат');

$res = $db->query("SELECT COUNT(*) AS cnt FROM `chat_mini`");
$cnt = ($row = $res->fetch()) ? $row['cnt'] : 0;
$pages = new pages($cnt);
$pages->this_page(); // получаем текущую страницу

$can_write = true;
/** @var $user \user */
if (!$user->is_writeable) {
    $doc->msg(__('Писать запрещено'), 'write_denied');
    $can_write = false;
}

if ($can_write && $pages->this_page == 1) {
    if (isset($_POST['send']) && isset($_POST['message']) && $user->group) {
        $message = (string) $_POST['message'];
        $users_in_message = text::nickSearch($message);
        $message = text::input_text($message);

        if ($dcms->censure && $mat = is_valid::mat($message)) {
            $doc->err(__('Обнаружен мат: %s', $mat));
        } elseif ($message) {
            $user->balls++;
            $res = $db->prepare("INSERT INTO `chat_mini` (`id_user`, `time`, `message`) VALUES (?, ?, ?)");
            $res->execute(Array($user->id, TIME, $message));
            header('Refresh: 1; url=?' . passgen() . '&' . SID);
            $doc->ret(__('Вернуться'), '?' . passgen());
            $doc->msg(__('Сообщение успешно отправлено'));
            exit;
        } else {
            $doc->err(__('Сообщение пусто'));
        }
    }

    if ($user->group) {
        $message_form = '';
        if (isset($_GET ['message']) && is_numeric($_GET ['message'])) {
            $id_message = (int) $_GET ['message'];
            $q = $db->prepare("SELECT * FROM `chat_mini` WHERE `id` = ? LIMIT 1");
            $q->execute(Array($id_message));
            if ($message = $q->fetch()) {
                $ank = new user($message['id_user']);
                if (isset($_GET['reply'])) {
                    $message_form = '@' . $ank->login . ',';
                } elseif (isset($_GET['quote'])) {
                    $message_form = "[quote id_user=\"{$ank->id}\" time=\"{$message['time']}\"]{$message['message']}[/quote]";
                }
            }
        }

        $form = new form('?' . passgen());
        $form->refresh_url('?' . passgen());
        $form->setAjaxUrl('ajax.php');
        $form->textarea('message', __('Сообщение'), $message_form);
        $form->button(__('Отправить'), 'send', false);
        $form->display();
    }
}

$listing = new listing();

// привязываем форму к листингу, чтобы листинг мог обновиться при отправке формы через AJAX
if (!empty($form))
    $listing->setForm($form);



$q = $db->query("SELECT * FROM `chat_mini` ORDER BY `id` DESC LIMIT $pages->limit");
if ($arr = $q->fetchAll()) {
    foreach ($arr AS $message) {
       $ank = new user($message['id_user']);
    $post = $listing->post();
    $post->id = 'chat_post_' . $message['id'];
    $post->url = 'actions.php?id=' . $message['id'];
    $post->time = misc::when($message['time']);
    $post->title = $ank->nick();
    $post->post = text::toOutput($message['message']);
    $post->icon($ank->icon());
    if (!$doc->last_modified)
        $doc->last_modified = $message['time'];
    }
}
$listing->setAjaxUrl('ajax.php?page=' . $pages->this_page);
$listing->display(__('Сообщения отсутствуют'));
$pages->display('?'); // вывод страниц

if ($user->group >= 3)
    $doc->act(__('Удаление сообщений'), 'message.delete_all.php');
?>