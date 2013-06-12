<?php

include_once '../sys/inc/start.php';
$doc = new document();
$doc->title = 'Просмотр сообщения';

if (!isset($_GET['id_message']) || !is_numeric($_GET['id_message'])) {
    header('Refresh: 1; url=./');
    $doc->err(__('Ошибка выбора сообщения'));
    exit;
}
$id_message = (int) $_GET['id_message'];

$q = $db->prepare("SELECT * FROM `forum_messages` WHERE `id` = ? AND `group_show` <= ?");
$q->execute(Array($id_message, $user->group));
if (!$message = $q->fetch()) {
    header('Refresh: 1; url=./');
    $doc->err(__('Сообщение не доступно'));
    exit;
}


$q = $db->prepare("SELECT * FROM `forum_themes` WHERE `id` = ?");
$q->execute(Array($message['id_theme']));

if (!$theme = $q->fetch()) {
    if (isset($_GET['return'])) {
        header('Refresh: 1; url=' . $_GET['return']);
    } else {
        header('Refresh: 1; url=./');
    }
    $doc->err(__('Тема не найдена'));
    exit;
}


if ($theme['group_show'] > $user->group) {
    $doc->err(__('Параметры темы не позволяют просматривать данное сообщение'));
    $doc->ret(__('В раздел'), 'topic.php?id=' . $message['id_topic']);
    exit;
}



$doc->title = $theme['name'];


$can_write = true;
if (!$user->is_writeable) {
    $doc->msg(__('Писать запрещено'), 'write_denied');
    $can_write = false;
}




$autor = new user((int) $message['id_user']);


if (!$autor->id) {
    $can_write = false;
}

if (isset($_GET['quote'])) {
    $re = "[quote id_user=\"{$autor->id}\" time=\"{$message['time']}\"]{$message['message']}[/quote]";
} else {
    $re = '@' . $autor->login . ', ';
}

if ($can_write && isset($_POST['message']) && $theme['group_write'] <= $user->group) {
    $message = (string) $_POST['message'];
    $users_in_message = text::nickSearch($message);
    $message_re = text::input_text($message);

    if ($dcms->forum_message_captcha && $user->group < 2 && (empty($_POST['captcha']) || empty($_POST['captcha_session']) || !captcha::check($_POST['captcha'], $_POST['captcha_session']))) {
        $doc->err(__('Проверочное число введено неверно'));
    } elseif ($dcms->censure && $mat = is_valid::mat($message_re)) {
        $doc->err(__('Обнаружен мат: %', $mat));
    } elseif ($message_re && text::input_text($re) != $message_re) {


        $res = $db->prepare("INSERT INTO `forum_messages` (`id_category`, `id_topic`, `id_theme`, `id_user`, `time`, `message`, `group_show`, `group_edit`)
 VALUES (?,?,?,?,?,?,?,?)");
        $res->execute(Array($theme['id_category'], $theme['id_topic'], $theme['id'], $user->id, TIME, $message_re, $theme['group_show'], $theme['group_edit']));
        $id_message = $db->lastInsertId();

        header('Refresh: 1; url=theme.php?id=' . $theme['id'] . '&page=end#message' . $id_message);


        $res = $db->prepare("UPDATE `forum_themes` SET `time_last` = ?, `id_last` = ? WHERE `id` = ? LIMIT 1");
        $res->execute(Array(TIME, $user->id, $theme['id']));
        $doc->msg(__('Ответ успешно отправлен'));


        if ($users_in_message) {
            for ($i = 0; $i < count($users_in_message) && $i < 20; $i++) {
                $user_id_in_message = $users_in_message[$i];
                if ($user_id_in_message == $user->id || ($user_id_in_message == $autor->id && $autor->notification_forum)) {
                    // самому себе уведомление не нужно, а автору сообщения отправим отдельно
                    continue;
                }
                $ank_in_message = new user($user_id_in_message);
                if ($ank_in_message->notice_mention) {
                    $res = $db->prepare("SELECT COUNT(*) AS cnt FROM `forum_messages` WHERE `id_theme` = ? AND `group_show` <= ?");
                    $res->execute(Array($theme['id'], $ank_in_message->group));
                    $count_posts_for_user = ($row = $res->fetch()) ? $row['cnt'] : 0;
                    $ank_in_message->mess("[user]{$user->id}[/user] упомянул" . ($user->sex ? '' : 'а') . " о Вас на форуме в [url=/forum/message.php?id_message={$id_message}]сообщении[/url] в теме [url=/forum/theme.php?id={$theme['id']}&postnum={$count_posts_for_user}#message{$id_message}]{$theme['name']}[/url]");
                }
            }
        }


        if ($autor->notification_forum && $user->id != $autor->id) {
            $res = $db->prepare("SELECT COUNT(*) FROM `forum_messages` WHERE `id_theme` = ? AND `group_show` <= ?");
            $res->execute(Array($theme['id'], $autor->group));
            $count_posts_for_user = ($row = $res->fetch()) ? $row['cnt'] : 0;
            $autor->mess("[user]{$user->id}[/user] ответил" . ($user->sex ? '' : 'а') . " Вам на форуме в [url=/forum/message.php?id_message={$id_message}]сообщении[/url] в теме [url=/forum/theme.php?id={$theme['id']}&postnum={$count_posts_for_user}#message{$id_message}]{$theme['name']}[/url]");
        }


        $doc->ret(__('В тему'), 'theme.php?id=' . $theme['id'] . '&amp;page=end#message' . $id_message);
        exit;
    } else {
        $doc->err(__('Нельзя оставить пустое сообщение'));
    }
}


if ($autor->id && $user->id) {
    $doc->title = __('Ответ для "%s"', $autor->login);
    //$can_write = false;
} else {
    $doc->title = __('Сообщение от "%s"', $autor->login);
}



$listing = new listing();
$post = $listing->post();
$post->title = $autor->nick();
$post->time = vremja($message['edit_time'] ? $message['edit_time'] : $message['time']);
$post->url = '/profile.view.php?id=' . $autor->id;
$post->icon($autor->icon());
$post->content = output_text($message['message']);
$listing->display();



if (!isset($_GET['files'])) {
    if ($can_write && $theme['group_write'] <= $user->group) {
        $smarty = new design();
        $smarty->assign('method', 'post');
        $smarty->assign('action', "?id_theme=$theme[id]&amp;id_message=$message[id]&amp;" . passgen() . (isset($_GET['return']) ? '&amp;return=' . urlencode($_GET['return']) : null));
        $elements = array();
        $elements[] = array('type' => 'textarea', 'title' => __('Ответ'), 'br' => 1, 'info' => array('name' => 'message', 'value' => $re));
        if ($dcms->forum_message_captcha && $user->group < 2)
            $elements[] = array('type' => 'captcha', 'session' => captcha::gen(), 'br' => 1);
        $elements[] = array('type' => 'submit', 'br' => 0, 'info' => array('value' => __('Отправить'))); // кнопка
        $smarty->assign('el', $elements);
        $smarty->display('input.form.tpl');
    }
}

$doc->ret(__('В тему'), 'theme.php?id=' . $message['id_theme']);
?>