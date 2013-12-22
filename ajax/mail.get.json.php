<?php

include '../sys/inc/start.php';
$mail = array();

$ank = new user((int) @$_GET['id_user']);

if ($user->group && $ank->group) {

    // только непрочитанные
    $only_unreaded = (int)isset($_GET['only_unreaded']);
    // отмечать все письма как прочитанные
    $set_readed = (int)isset($_GET['set_readed']);
    // с выбранного времени
    $time_from = (int)@$_GET['time_from'];

    if ($set_readed) {
        // отмечаем письма от этого человека как прочитанные
        $res = $db->prepare("UPDATE `mail` SET `is_read` = '1' WHERE `id_user` = ? AND `id_sender` = ?");
        $res->execute(Array($user->id, $ank->id));
    }

    $q = $db->prepare("SELECT * FROM `mail` WHERE `time` > ? " . ($only_unreaded ? ' AND `is_read` = 0' : '') . " AND ((`id_user` = ? AND `id_sender` = ?) OR (`id_user` = ? AND `id_sender` = ?)) ORDER BY `id` DESC");
    $q->execute(Array($time_from, $user->id, $ank->id, $ank->id, $user->id));
    while ($qmail = $q->fetch()) {
        $mail[] = array('id' => (int)$qmail['id'],
            'id_sender' => (int)$qmail['id_sender'],
            //'id_user' => (int)$qmail['id_user'],
            'mess' => text::toOutput($qmail['mess']),
            'time' => (int)$qmail['time'],
            'is_read' => (bool)$qmail['is_read']

        );
    }
}

header('Content-type: application/json');
echo json_encode($mail);
?>