<?php

include_once '../sys/inc/start.php';
$doc = new document(2);
$doc->title = __('Удаление комментария');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    if (isset($_GET['return']))
        header('Refresh: 1; url=' . $_GET['return']);
    else
        header('Refresh: 1; url=./');
    $doc->err(__('Ошибка выбора комментария'));
    exit;
}
$id_message = (int) $_GET['id'];

$q = $db->prepare("SELECT * FROM `news_comments` WHERE `id` = ? LIMIT 1");
$q->execute(Array($id_message));

if (!$message = $q->fetch()) {
    if (isset($_GET['return']))
        header('Refresh: 1; url=' . $_GET['return']);
    else
        header('Refresh: 1; url=./');
    $doc->err(__('Комментарий не найден'));
    exit;
}


$res = $db->prepare("DELETE FROM `news_comments` WHERE `id` = ? LIMIT 1");
$res->execute(Array($id_message));
$doc->msg(__('Комментарий успешно удален'));

if (isset($_GET['return']))
    header('Refresh: 1; url=' . $_GET['return']);
else
    header('Refresh: 1; url=./');
if (isset($_GET['return']))
    $doc->ret(__('Вернуться'), text::toValue($_GET['return']));
else
    $doc->ret(__('Вернуться'), './');
?>