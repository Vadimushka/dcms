<?php
include_once '../sys/inc/start.php';
$doc = new document(1);
$doc->theme = __('Редактирование сообщения');

if (isset($_GET['return']))
    header('Refresh: 2; url=' . $_GET['return']);
else
    header('Refresh: 2; url=./');


if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $doc->err(__('Ошибка выбора сообщения'));
    exit;
}

$id_message = (int)$_GET['id'];
$q = $db->prepare("SELECT * FROM `forum_messages` WHERE `id` = ?");
$q->execute(Array($id_message));

if (!$message = $q->fetch()) {
    $doc->err(__('Сообщение не найдено'));
    exit;
}

if ($message['id_user'] == $user->id) {
    $doc->err(__('Нельзя голосовать за свое сообщение'));
    exit;
}

$q = $db->prepare("SELECT * FROM `forum_rating` WHERE `id_message` = ? AND `id_user` = ?");
$q->execute(Array($id_message, $user->id));

if ($q->fetch()) {
    $doc->err(__('Вы уже голосовали за это сообщение'));
    exit;
}

switch (@$_GET['change']) {
    case 'up':
        $rating = 1;
        break;
    case 'down':
        $rating = -1;
        break;
    default:
        exit;
}

$res = $db->prepare("INSERT INTO `forum_rating` (`id_message`, `id_user`, `time`, `rating`) VALUES (:id_msg, :id_user, :t, :rating)");
$res->execute(array(':id_msg' => $id_message, ':id_user' => $user->id, ':t' => TIME, ':rating' => $rating));

$res = $db->prepare("UPDATE `forum_messages` AS `fm`
SET `fm`.`rating` = (SELECT SUM(`rating`) FROM `forum_rating` AS `fr` WHERE `fr`.`id_message` = :id_msg),
`fm`.`rating_up` = (SELECT SUM(`rating`) FROM `forum_rating` AS `fr` WHERE `fr`.`id_message` = :id_msg AND `rating` > 0),
`fm`.`rating_down` = (SELECT SUM(`rating`) FROM `forum_rating` AS `fr` WHERE `fr`.`id_message` = :id_msg AND `rating` < 0) * -1
 WHERE `fm`.`id` = :id_msg LIMIT 1");
$res->execute(array(':id_msg' => $id_message));


$res = $db->prepare("INSERT INTO `reviews_users` (`id_user`, `id_ank`, `time`, `forum_message_id`, `rating`) VALUES (?, ?, ?, ?, ?)");
$res->execute(Array(0, $message['id_user'], TIME, $id_message, $rating * 0.1));

$res = $db->prepare("UPDATE `users` AS `u` SET `u`.`rating` = (SELECT SUM(`rating`) FROM `reviews_users` AS `ru` WHERE `ru`.`id_ank` = :id_user) WHERE `u`.`id` = :id_user LIMIT 1");
$res->execute(Array(':id_user' =>$message['id_user']));

$doc->msg(__('Ваш голос успешно учтен'));