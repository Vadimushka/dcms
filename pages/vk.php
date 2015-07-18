<?php
include_once '../sys/inc/start.php';
$doc = new document();
$doc->title = __('Вход vk.com');

if (!$dcms->vk_auth_enable) {
    $doc->err(__('Авторизация через vk.com не доступна'));
    exit;
}

if (!empty($_GET['error'])) {
    if (!empty($_GET['error_description'])) {
        $doc->err(text::toOutput($_GET['error_description']));
    } else {
        $doc->err(__('Не удалось авторизоваться'));
    }
    exit;
}

if (empty($_GET['code'])) {
    header("Location: /");
    exit;
}

if (!$dcms->vk_app_id || !$dcms->vk_app_secret){
    header("Location: /");
    exit;
}

if(!function_exists('logaut')) {
function logaut($id, $method, $status, $vk_id) {
        global $db, $dcms; /* будем получать IP, ID браузера, домен, делать запросы */
        $ua = (string) @$_SERVER['HTTP_USER_AGENT']; // (string) USER_AGENT
        
        $q = $db->prepare("SELECT * FROM `log_of_user_aut` WHERE `id_user` = :id AND `user_id' = :uid AND `iplong` = :ip_long AND `browser_ua` = :ua AND `domain` = :domain AND `method` = :method AND `status` = :status ORDER BY `time` DESC LIMIT 1");
        $q->execute(Array(':id' => $id, ':ip_long' => $dcms->ip_long, ':ua' => $ua, ':domain' => $dcms->subdomain_main, ':method' => $method, ':status' => $status));

        if(!$row = $q->fetch()) { /* якщо записи з такими параметрами відсутні, то додаємо у базу */
//            mysql_query("INSERT INTO `log_of_user_aut` (`id_user`,`method`,`iplong`,`time`,`id_browser`,`browser`,`browser_ua`,`domain`,`status`) VALUES ('$id','$method','$dcms->ip_long','" . TIME . "','$dcms->browser_id','$dcms->browser_name','$ua','" . $dcms->subdomain_main . "','$status')");
            $res = $db->prepare("INSERT INTO `log_of_user_aut` (`id_user`,`user_id`,`method`,`iplong`, `time`, `id_browser`,`browser`,`browser_ua`,`domain`,`status`) VALUES (:id,:uid,:method,:ip_long,:time,:br_id,:br_name,:ua,:domain,:status)");
            $res->execute(Array(':id' => $id, ':uid' => $vk_id, ':ip_long' => $dcms->ip_long, ':ua' => $ua, ':domain' => $dcms->subdomain_main, ':method' => $method, ':status' => $status, ':br_id' => $dcms->browser_id, ':br_name' => $dcms->browser_name, ':time' => TIME));
        } else {
//            mysql_query("UPDATE `log_of_user_aut` SET `time` = '" . TIME . "', `count` = `count` + 1 WHERE `id_user` = '$id' AND `iplong` = '" . $dcms->ip_long . "' AND `browser_ua` = '$ua' AND `domain` = '" . $dcms->subdomain_main . "' AND `method` = '$method' AND `status` = '$status' LIMIT 1");
            $res = $db->prepare("UPDATE `log_of_user_aut` SET `time` = :time, `id_browser` = :br_id, `count` = `count` + 1 WHERE `id_user` = :id AND `user_id' = :uid AND `iplong` = :ip_long AND `browser_ua` = :ua AND `domain` = :domain AND `method` = :method AND `status` = :status LIMIT 1");
            $res->execute(Array(':id' => $id, ':uid' => $vk_id, ':ip_long' => $dcms->ip_long, ':ua' => $ua, ':domain' => $dcms->subdomain_main, ':method' => $method, ':status' => $status, ':br_id' => $dcms->browser_id, ':time' => TIME));
        }
    }
}

try{
    $vk = new vk($dcms->vk_app_id, $dcms->vk_app_secret);
    $vk->getAccessToken('http://' . $_SERVER['HTTP_HOST'] . '/vk.php', $_GET['code']);
    $vk_user = $vk->getCurrentUser();

    echo '<!--'.json_encode($vk_user).'-->';

    if ($vk->getEmail()) {
        $q = $db->prepare("SELECT * FROM `users` WHERE `reg_mail` = :email ORDER BY `last_visit` DESC LIMIT 1");
        $q->execute(array(':email' => $vk->getEmail()));
        if ($q->rowCount()) {
            $user_data = $q->fetch();
            logaut($user_data['id'], 'vk', 1, $vk_user['uid']);
            $_SESSION [SESSION_ID_USER] = $user_data['id'];
            $doc->msg(__("Авторизация прошла успешно"));
            exit;
        }
    }

    $q = $db->prepare("SELECT * FROM `users` WHERE `vk_id` = :id_vk LIMIT 1");
    $q->execute(array(':id_vk' => $vk_user['uid']));
    if ($q->rowCount()) {
        $user_data = $q->fetch();
        logaut($user_data['id'], 'vk', 1, $vk_user['uid']);
        $_SESSION [SESSION_ID_USER] = $user_data['id'];
        if (empty($user_data['reg_mail']) && $vk->getEmail()) {
                  $q = $db->prepare("UPDATE `users` SET `reg_mail` = :email WHERE `vk_id` = :vk_id LIMIT 1");
                  $q->execute(array(':vk_id' => $vk_user['uid'], ':email' => $vk->getEmail())); }
        $doc->msg(__("Авторизация прошла успешно"));
        exit;
    } else if (!$dcms->vk_reg_enable) {
        logaut(0, 'vk', 0, $vk_user['uid']);
        throw new Exception(__('Регистрация через vk.com запрещена'));
    }

    $res = $db->prepare("SELECT * FROM `users` WHERE login =?;");
    $res->execute(Array($vk_user['domain'])); // проверяем не занят-ли логин
    $login = (!$res->fetch() && is_valid::nick($vk_user['domain']) && $vk_user['domain'] != "id".$vk_user['uid']) ? $vk_user['domain'] : '$vk.' . $vk_user['uid'];

    $res = $db->prepare("INSERT INTO `users` (`reg_date`, `login`, `password`, `sex`, `reg_mail`, `vk_id`, `vk_first_name`, `vk_last_name`, `realname`, `lastname`, `email`, `language`)
VALUES (:reg_date, :login, :pass, :sex, :reg_mail, :vk_id, :vk_first_name, :vk_last_name, :vk_first_name, :vk_last_name, :reg_mail, :language)");
    $res->execute(Array(
        ':reg_date' => TIME,
        ':login' => $login,
        ':pass' => $vk->getAccessToken(),
        ':sex' => ($vk_user['sex'] == 0 || $vk_user['sex'] == 2) ? 1 : 0,
        ':reg_mail' => $vk->getEmail(),
        ':vk_id' => $vk_user['uid'],
        ':vk_first_name' => $vk_user['first_name'],
        ':vk_last_name' => $vk_user['last_name'],
        ':language' => $user_language_pack->code
    ));

    $id = $db->lastInsertId();
    $_SESSION [SESSION_ID_USER] = $id;
    $doc->msg(__("Регистрация прошла успешно"));
}catch (Exception $e){
    $doc->err(__('Не удалось авторизоваться: %s', $e->getMessage()));
    exit;
}
