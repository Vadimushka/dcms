<?php

include_once '../sys/inc/start.php';
$doc = new document(2);
$doc->title = __('Удаление сообщения');

if (!isset($_GET ['id']) || !is_numeric($_GET ['id'])) {
    if (isset($_GET ['return']))
        header('Refresh: 1; url=' . $_GET ['return']);
    else
        header('Refresh: 1; url=./');
    $doc->err(__('Ошибка выбора сообщения'));
    exit();
}
$id_message = (int) $_GET ['id'];

$q = $db->prepare("SELECT * FROM `chat_mini` WHERE `id` = ? LIMIT 1");
$q->execute(Array($id_message));

if (!$message = $q->fetch()) {
    if (isset($_GET ['return']))
        header('Refresh: 1; url=' . $_GET ['return']);
    else
        header('Refresh: 1; url=./');
    $doc->err(__('Сообщение не найдено'));
    exit();
}


$res = $db->prepare("DELETE FROM `chat_mini` WHERE `id` = ? LIMIT 1");
$res->execute(Array($id_message));
$doc->msg(__('Сообщение успешно удалено'));

$ank = new user($message ['id_user']);

$dcms->log('Мини чат', "Удаление сообщения от [url=/profile.view.php?id={$ank->id}]{$ank->login}[/url] ([vremja]$message[time][/vremja]):\n" . $message ['message']);

if (isset($_GET ['return']))
    header('Refresh: 1; url=' . $_GET ['return']);
else
    header('Refresh: 1; url=./?' . SID);
if (isset($_GET ['return']))
    $doc->ret(__('Вернуться'), for_value($_GET ['return']));
else
    $doc->ret(__('Вернуться'), './');
?>