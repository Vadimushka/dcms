<?php

include_once '../sys/inc/start.php';
dpanel::check_access();
$groups = groups::load_ini();
$doc = new document(4);
$doc->title = __('Удаление пользователя');

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


$tables = ini::read(H . '/sys/ini/user.tables.ini', true);


if (isset($_POST['delete'])) {
    if (empty($_POST['captcha']) || empty($_POST['captcha_session']) || !captcha::check($_POST['captcha'], $_POST['captcha_session'])) {
        $doc->err(__('Проверочное число введено неверно'));
    } else {

        misc::user_delete($ank->id);
        $dcms->log('Пользователи', 'Удаление пользователя ' . $ank->login . ' (ID ' . $ank->id . ')');

        $doc->msg(__('Пользователь успешно удален'));


        $doc->ret(__('Админка'), '/dpanel/');
        exit;
    }
}

foreach ($tables AS $name => $v) {
    $count = @mysql_result(mysql_query("SELECT COUNT(*) FROM `" . my_esc($v['table']) . "` WHERE `" . my_esc($v['row']) . "` = '$ank->id'"), 0);
    echo output_text(($count ? '[b]' : '') . $name . ': ' . $count . ($count ? '[/b]' : '')) . "<br />\n";
}




$smarty = new design();
$smarty->assign('method', 'post');
$smarty->assign('action', "?id_ank=$ank->id&amp;" . passgen());
$elements = array();
$elements [] = array('type' => 'text', 'value' => output_text(__('Пользователь будет удален без возможности восстановления. Подтвердите удаление пользователя "%s".', $ank->login)), 'br' => 1);

$elements[] = array('type' => 'captcha', 'session' => captcha::gen(), 'br' => 1);
$elements[] = array('type' => 'submit', 'br' => 0, 'info' => array('name' => 'delete', 'value' => __('Удалить'))); // кнопка
$smarty->assign('el', $elements);
$smarty->display('input.form.tpl');

$doc->ret(__('Действия'), 'user.actions.php?id=' . $ank->id);
$doc->ret(__('Анкета "%s"', $ank->login), '/profile.view.php?id=' . $ank->id);
$doc->ret(__('Админка'), '/dpanel/');
?>
