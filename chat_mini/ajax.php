<?php

include_once '../sys/inc/start.php';
$json = array();
$skip_ids = explode(',', @$_POST['skip_ids']);

$json['remove'] = $skip_ids;
$json['add'] = array();

$pages = new pages;
$pages->posts = mysql_result(mysql_query("SELECT COUNT(*) FROM `chat_mini`"), 0); // количество сообщений
$pages->this_page(); // получаем текущую страницу

$q = mysql_query("SELECT * FROM `chat_mini` ORDER BY `id` DESC LIMIT $pages->limit");

$after_id = false;
while ($message = mysql_fetch_assoc($q)) {
    $id_post = 'chat_post_' . $message['id'];
    
    if (in_array($id_post, $skip_ids)) {
        $key = array_search($id_post, $json['remove']);
        unset($json['remove'][$key]);
    } else {
        $ank = new user($message['id_user']);
        $post = new listing_post();
        $post->id = $id_post;
        $post->url = 'actions.php?id=' . $message['id'];
        $post->time = vremja($message['time']);
        $post->title = $ank->nick();
        $post->post = output_text($message['message']);
        $post->icon($ank->icon());

        $json['add'][] = array(
            'after_id' => $after_id,
            'html' => $post->fetch()
        );
    }
    $after_id = $id_post;
}
$json['remove'] = array_values($json['remove']);
header('Content-type: application/json');
echo json_encode($json);
?>
