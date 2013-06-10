<?php

include '../sys/inc/start.php';
$friends = array();

if ($user->id) {
    $q = $db->prepare("SELECT * FROM `friends` WHERE `id_user` = ? ORDER BY `confirm` ASC, `time` DESC");
    $q->execute(Array($user->id));
    if ($arr = $q->fetchAll()) {
        foreach ($arr AS $friend) {
            $ank = new user($friend['id_friend']);
            $friends[] = array('confirm' => $friend['confirm'],
                'id_friend' => $friend['id_friend'],
                'login' => $ank->login,
                'online' => $ank->online,
                'time' => $friend['time']
            );
        }
    }
}


header('Content-type: application/json');
echo json_encode($friends);
?>
