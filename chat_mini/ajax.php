<?php

include_once '../sys/inc/start.php';
$json = array();

if (isset($_POST['send'])) {
    if (!$user->is_writeable) {
        $json['err'] = __('Писать запрещено');
    } elseif (isset($_POST['message']) && $user->group) {
        $message = (string) $_POST['message'];
        $users_in_message = text::nickSearch($message);
        $message = text::input_text($message);

        if ($dcms->censure && $mat = is_valid::mat($message)) {
            $json['err'] = __('Обнаружен мат: %s', $mat);
        } elseif ($message) {
            $user->balls++;
            $res = $db->prepare("INSERT INTO `chat_mini` (`id_user`, `time`, `message`) VALUES (?, ?, ?)");
            $res->execute(Array($user->id, TIME, $message));

            $json['msg'] = __('Сообщение успешно отправлено');
        } else {
            $json['err'] = __('Сообщение пусто');
        }
    } else {
        $json['err'] = __('Неизвестная ошибка');
    }
    $json['form']['message'] = '';
} else {
    $skip_ids_str = @$_POST['skip_ids'];
    $skip_ids = $skip_ids_str ? explode(',', $skip_ids_str) : array();

    $json['remove'] = $skip_ids;
    $json['add'] = array();


    $res = $db->query("SELECT COUNT(*) AS cnt FROM `chat_mini`");
    $cnt = ($row = $res->fetch()) ? $row['cnt'] : 0;
    $pages = new pages($cnt);
    $pages->this_page(); // получаем текущую страницу

    $q = $db->query("SELECT * FROM `chat_mini` ORDER BY `id` DESC LIMIT $pages->limit");

    $after_id = false;
    if ($arr = $q->fetchAll()) {
        foreach ($arr AS $message) {
            $id_post = 'chat_post_' . $message['id'];

        if (in_array($id_post, $skip_ids)) {
            $key = array_search($id_post, $json['remove']);
            unset($json['remove'][$key]);
        } else {
            $ank = new user($message['id_user']);
            $post = new listing_post();
            $post->id = $id_post;
            $post->url = 'actions.php?id=' . $message['id'];
            $post->time = misc::when($message['time']);
            $post->title = $ank->nick();
            $post->post = text::toOutput($message['message']);
            $post->icon($ank->icon());

                $json['add'][] = array(
                    'after_id' => $after_id,
                    'html' => $post->fetch()
                );
            }
            $after_id = $id_post;
        }
    }
    $json['remove'] = array_values($json['remove']);
    $json['pages'] = $pages;
}

header('Content-type: application/json; charset=utf-8', true);
echo json_encode($json);
?>
