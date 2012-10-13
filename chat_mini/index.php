<?php

include_once '../sys/inc/start.php';
$doc = new document();
$doc->title = __('Мини чат');


$can_write = true;
if (!$user->is_writeable) {
    $doc->msg(__('Писать запрещено'), 'write_denied');
    $can_write = false;
}

if ($can_write) {
    if (isset($_POST['send']) && isset($_POST['message']) && $user->group) {
        $message = (string) $_POST['message'];
        $users_in_message = text::nickSearch($message);
        $message = text::input_text($message);

        if ($dcms->censure && $mat = is_valid::mat($message)) {
            $doc->err(__('Обнаружен мат: %s', $mat));
        } elseif ($message) {
            $user->balls++;
            mysql_query("INSERT INTO `chat_mini` (`id_user`, `time`, `message`) VALUES ('$user->id', '" . TIME . "', '" . my_esc($message) . "')");
            header('Refresh: 1; url=?' . passgen() . '&' . SID);
            $doc->ret(__('Вернуться'), '?' . passgen());
            $doc->msg(__('Сообщение успешно отправлено'));




            if ($users_in_message) {
                for ($i = 0; $i < count($users_in_message) && $i < 20; $i++) {
                    $user_id_in_message = $users_in_message[$i];
                    if ($user_id_in_message == $user->id) {
                        continue;
                    }
                    $ank_in_message = new user($user_id_in_message);

                    if ($ank_in_message->notice_mention) {
                        $ank_in_message->mess("[user]{$user->id}[/user] упомянул" . ($user->sex ? '' : 'а') . " о Вас в [url=/chat_mini/]Мини-чате[/url]");
                    }
                }
            }



            exit;
        } else {
            $doc->err(__('Сообщение пусто'));
        }
    }

    if ($user->group) {



        $message_form = '';
        if (isset($_GET ['message']) && is_numeric($_GET ['message'])) {
            $id_message = (int) $_GET ['message'];

            $q = mysql_query("SELECT * FROM `chat_mini` WHERE `id` = '$id_message' LIMIT 1");

            if (mysql_num_rows($q)) {
                $message = mysql_fetch_assoc($q);

                $ank = new user($message['id_user']);
                if (isset($_GET['reply'])) {
                    $message_form = '@' . $ank->login . ',';
                } elseif (isset($_GET['quote'])) {
                    $message_form = "[quote id_user=\"{$ank->id}\" time=\"{$message['time']}\"]{$message['message']}[/quote]";
                }
            }
        }



        $smarty = new design();
        $smarty->assign('method', 'post');
        $smarty->assign('action', '?' . passgen());
        $elements = array();
        $elements[] = array('type' => 'textarea', 'title' => __('Сообщение'), 'br' => 1, 'info' => array('name' => 'message', 'value' => $message_form));
        $elements[] = array('type' => 'submit', 'br' => 0, 'info' => array('name' => 'send', 'value' => __('Отправить'))); // кнопка
        $elements[] = array('type' => 'submit', 'br' => 0, 'info' => array('name' => 'refresh', 'value' => __('Обновить'))); // кнопка
        $smarty->assign('el', $elements);
        $smarty->display('input.form.tpl');
    }
}


$listing = new listing;

$pages = new pages;
$pages->posts = mysql_result(mysql_query("SELECT COUNT(*) FROM `chat_mini`"), 0); // количество сообщений
$pages->this_page(); // получаем текущую страницу

$q = mysql_query("SELECT * FROM `chat_mini` ORDER BY `id` DESC LIMIT $pages->limit");
while ($message = mysql_fetch_assoc($q)) {
    $ank = new user($message['id_user']);

    $post = $listing->post();

    $post->url = 'actions.php?id=' . $message['id'];


    $post->time = vremja($message['time']);
    $post->title = $ank->nick();
    $post->post = output_text($message['message']);
    $post->icon($ank->icon());
}


$listing->display(__('Сообщения отсутствуют'));
$pages->display('?'); // вывод страниц

if ($user->group >= 3) {
    $doc->act(__('Удаление сообщений'), 'message.delete_all.php');
}
?>