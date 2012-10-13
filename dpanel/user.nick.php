<?php

include_once '../sys/inc/start.php';
dpanel::check_access();
$doc = new document(4);
$doc->title = __('Изменение ника');

if (isset($_GET['id_ank']))
    $ank = new user($_GET['id_ank']);
else
    $ank = $user;

if (!$ank->group) {
    if (isset($_GET['return']))
        header('Refresh: 1; url=' . $_GET['return']);
    else
        header('Refresh: 1; url=/');

    $doc->err(__('Нет данных'));
    exit;
}

$doc->title .= ' "' . $ank->login . '"';

if ($ank->group >= $user->group) {
    if (isset($_GET['return']))
        header('Refresh: 1; url=' . $_GET['return']);
    else
        header('Refresh: 1; url=/');

    $doc->err(__('Ваш статус не позволяет производить действия с данным пользователем'));
    exit;
}

if (isset($_POST['save']) && !empty($_POST['login']) && $_POST['login'] != $ank->login) {
    $login = (string) $_POST['login'];

    if (!is_valid::nick($login))
        $doc->err(__('Не корректный Ник'));
    elseif ($login != my_esc($login))
        $doc->msg(__('В нике содержатся запрещенные символы'));
    elseif (mysql_result(mysql_query("SELECT COUNT(*) FROM `users` WHERE `login` = '" . my_esc($login) . "'"), 0))
        $doc->err(__('Пользователь с таким ником уже зарегистрирован'));
    else {
        $dcms->log('Пользователи', 'Изменение ника пользователя ' . $ank->login . ' на [url=/profile.view.php?id=' . $ank->id . ']' . $login . '[/url]');

        $ank->login = $login;
        $doc->msg(__('Ник успешно изменен'));
    }
}

$smarty = new design();
$smarty->assign('method', 'post');
$smarty->assign('action', "?id_ank=$ank->id&amp;" . passgen() . (isset($_GET['return']) ? '&amp;return=' . urlencode($_GET['return']) : null));
$elements = array();

$elements[] = array('type' => 'input_text', 'title' => __('Логин/ник'), 'br' => 1, 'info' => array('name' => 'login', 'value' => $ank->login));

$elements[] = array('type' => 'submit', 'br' => 0, 'info' => array('name' => 'save', 'value' => __('Применить'))); // кнопка
$smarty->assign('el', $elements);
$smarty->display('input.form.tpl');

$doc->ret(__('Действия'), 'user.actions.php?id=' . $ank->id);
$doc->ret(__('Анкета "%s"', $ank->login), '/profile.view.php?id=' . $ank->id);
$doc->ret(__('Админка'), '/dpanel/');
?>
