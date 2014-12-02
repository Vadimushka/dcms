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

$code = $_GET['code'];

/** @var dcms $dcms */
$http_client = new http_client('https://oauth.vk.com/access_token?client_id' . $dcms->vk_app_id . '&client_secret=' . $dcms->vk_app_secret . '&code=' . $_GET['code'] . '&redirect_uri=' . urlencode('http://' . $_SERVER['HTTP_HOST'] . '/vk.php'));
$json_content = $http_client->getContent();

if (false === ($data = json_decode($json_content, true)) || empty($data['access_token'])) {
    $doc->err(__('Не удалось авторизоваться: %s', __('Не получен access_token')));
    exit;
}

$vk_user_id = $data['user_id'];
$vk_access_token = $data['access_token'];
$vk_email = $data['email'];

$q = $db->prepare("SELECT * FROM `users` WHERE `vk_id` = :id_vk LIMIT 1");
$q->execute(array(':id_vk' => $vk_user_id));
if ($q->rowCount()) {
    $user_data = $q->fetch();
    $res = $db->prepare("INSERT INTO `log_of_user_aut` (`id_user`,`method`,`iplong`, `time`, `id_browser`, `status`) VALUES (?,'vk',?,?,?,'1')");
    $res->execute(Array($user_data['id'], $dcms->ip_long, TIME, $dcms->browser_id));
    $_SESSION [SESSION_ID_USER] = $user_data['id'];
    $doc->msg("Авторизация прошла успешно");
    exit;
} else if (!$dcms->vk_reg_enable) {
    $doc->err(__('Регистрация через vk.com не доступна'));
    exit;
}

$http_client = new http_client("https://api.vk.com/method/account.getProfileInfo?access_token=" . $vk_access_token);
$json_content = $http_client->getContent();

if (false === ($data = json_decode($json_content, true))) {
    $doc->err(__('Не удалось авторизоваться: %s', __('Не получены данные пользователя')));
    exit;
}

$res = $db->prepare("INSERT INTO `users` (`reg_date`, `login`, `password`, `sex`, `reg_mail`, `vk_id`, `vk_first_name`, `vk_last_name`)
VALUES (:reg_date, :login, :pass, :sex, :reg_mail, :vk_id, :vk_first_name, :vk_last_name)");
$res->execute(Array(
    ':reg_date' => TIME,
    ':login' => '$vk.' . $vk_user_id,
    ':pass' => $vk_access_token,
    ':sex' => ($data['sex'] == 0 || $data['sex'] == 2) ? 1 : 0,
    ':reg_mail' => '',
    ':vk_id' => $vk_user_id,
    ':vk_first_name' => $data['first_name'],
    ':vk_last_name' => $data['last_name']
));
