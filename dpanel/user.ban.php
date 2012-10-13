<?php

include_once '../sys/inc/start.php';
dpanel::check_access();
$doc = new document(2);
$doc->title = __('Бан пользователя');

$ank = new user(@$_GET ['id_ank']);

if (!$ank->group) {
    if (isset($_GET ['return']))
        header('Refresh: 1; url=' . $_GET ['return']);
    else
        header('Refresh: 1; url=/');

    $doc->err(__('Нет данных о пользователе'));
    exit();
}

if ($ank->group >= $user->group) {
    if (isset($_GET ['return']))
        header('Refresh: 1; url=' . $_GET ['return']);
    else
        header('Refresh: 1; url=/');

    $doc->err(__('Недостаточно привилегий'));
    exit();
}

$doc->title = __('Бан "%s"', $ank->login);

$link = !empty($_GET ['link']) ? $_GET ['link'] : (!empty($_POST ['link']) ? $_POST ['link'] : false);
$code = !empty($_GET ['code']) ? $_GET ['code'] : (!empty($_POST ['code']) ? $_POST ['code'] : false);

$codes = new menu_code('code');

if (!$code && !isset($_GET ['skip'])) {
    $smarty = new design ();
    $smarty->assign('method', 'post');
    $smarty->assign('action', '?id_ank=' . $ank->id . '&amp;link=' . urlencode($link) . (isset($_GET ['return']) ? '&amp;return=' . urlencode($_GET ['return']) : null));
    $elements = array();
    $elements [] = array('type' => 'input_text', 'title' => '<a' . ($dcms->browser_type == 'web' ? ' target="_blank"' : null) . ' href="' . for_value($link) . '">' . __('Ссылка') . '</a>', 'br' => 1, 'info' => array('name' => 'link', 'value' => $link));
    $elements [] = array('type' => 'select', 'br' => 1, 'title' => __('Нарушение'), 'info' => array('name' => 'code', 'options' => $codes->options()));
    $elements [] = array('type' => 'submit', 'br' => 0, 'info' => array('value' => __('Далее'))); // кнопка
    $smarty->assign('el', $elements);
    $smarty->display('input.form.tpl');
    if (isset($_GET ['return'])) {
        $doc->ret(__('Вернуться'), for_value($_GET ['return']));
    }
    $doc->ret(__('В анкету'), '/profile.view.php?id=' . $ank->id);
    $doc->ret(__('Админка'), './');
    exit();
}
// получение минимального и максимального срока банов
list ( $min, $max ) = $codes->get_time($code);

if (isset($_POST ['ban'])) {
    $comm = text::input_text(@$_POST ['comment']);
    $access_view = (int) empty($_POST ['full']);

    $time_1 = abs((int) @$_POST ['time']);
    switch (@$_POST ['timem']) {
        case 'forever':
            $time_ban_end = 'NULL';
            break;
        case 'm' :
            $time_ban_end = $time_1 * 60 + TIME;
            break;
        case 'h' :
            $time_ban_end = $time_1 * 3600 + TIME;
            break;
        case 'd' :
            $time_ban_end = $time_1 * 86400 + TIME;
            break;
        case 'ms' :
            $time_ban_end = $time_1 * 2592000 + TIME;
            break;
    }



    if (!$time_1)
        $doc->err(__('Не корректное время бана'));
    elseif (!isset($codes->menu_arr [$code]))
        $doc->err(__('Не выбрано нарушение'));
    elseif (!$comm)
        $doc->err(__('Необходимо прокомментировать бан'));
    elseif ($link && mysql_result(mysql_query("SELECT COUNT(*) FROM `ban` WHERE `id_user` = '$ank->id' AND `link` = '" . my_esc($link) . "' AND `code` = '" . my_esc($code) . "'"), 0))
        $doc->err(__('Нельзя банить пользователя несколько раз за одно и то же нарушение'));
    else {
        // делаем все жалобы обработанными
        mysql_query("UPDATE `complaints` SET `processed` = '1' WHERE `id_ank` = '$ank->id' AND `link` = '" . my_esc($link) . "' AND `code` = '" . my_esc($code) . "'");
        // ставим запись о бане
        mysql_query("INSERT INTO `ban` (`id_user`, `id_adm`, `link`, `code`, `comment`, `time_start`, `time_end`, `access_view`)
VALUES ('$ank->id', '$user->id', '" . my_esc($link) . "', '" . my_esc($code) . "', '" . my_esc($comm) . "', '" . TIME . "', $time_ban_end, '$access_view') ");
        mysql_query("UPDATE `users` SET `is_ban` = '1' WHERE `id` = '$ank->id'");

        $dcms->log('Пользователи', 'Бан пользователя [user]' . $ank->id . '[/user] на' . ($time_ban_end == 'NULL' ? 'всегда' : (' ' . vremja($time_ban_end) )) . "\nПричина: $comm");

        if ($time_ban_end == 'NULL') {
            $doc->msg(__('Пользователь успешно забанен навсегда'));
        } else {
            $doc->msg(__('Пользователь успешно забанен на %s', vremja($time_ban_end)));
        }
    }
}

if (!empty($_GET['ban_delete'])) {
    $id_ban_delete = (int) $_GET['ban_delete'];
    $q = mysql_query("SELECT * FROM `ban` WHERE `id_user` = '$ank->id' AND `id` = '$id_ban_delete'");
    if (!mysql_num_rows($q)) {
        $doc->err(__('Ошибка снятия бана'));
    } else {
        $ban = mysql_fetch_assoc($q);
        $adm = new user($ban['id_adm']);
        if ($adm->group < $user->group || $adm->id == $user->id) {

            mysql_query("DELETE FROM `ban` WHERE `id` = '$id_ban_delete' LIMIT 1");

            $doc->msg(__('Бан успешно снят'));
        } else {
            $doc->err(__('Недостаточно привилегий'));
        }
    }
}



$listing = new listing();
// список нарушений
$q = mysql_query("SELECT * FROM `ban` WHERE `id_user` = '$ank->id' ORDER BY `id` DESC");
while ($c = mysql_fetch_assoc($q)) {
    $post = $listing->post();
    $adm = new user($c ['id_adm']);

    $post->title = $adm->nick();
    $post->time = vremja($c ['time_start']);

    $post->action('delete', '?id_ank=' . $ank->id . '&amp;ban_delete=' . $c['id'].'&amp;skip');

    $post->content = __('Нарушение: %s', for_value($c['code'])) . "\n";
    if ($c ['time_start'] && TIME < $c ['time_start']) {
        $post->content .= '[b]' . __('Начало действия') . ':[/b]' . vremja($c ['time_start']) . "\n";
    }
    if ($c['time_end'] === NULL) {
        $post->content .= '[b]' . __('Пожизненная блокировка') . "[/b]\n";
    } elseif (TIME < $c['time_end']) {
        $post->content .= __('Осталось: %s', vremja($c['time_end'])) . "\n";
    }
    if ($c['link']) {
        $post->content .= __('Ссылка на нарушение: %s', $c['link']) . "\n";
    }

    $post->content .= __('Комментарий: %s', $c['comment']) . "\n";


    $post->content = output_text($post->content);
}

$listing->display(__('Нарушения отсутствуют'));



$smarty = new design ();
$smarty->assign('method', 'post');
$smarty->assign('action', '?id_ank=' . $ank->id . '&amp;code=' . urlencode($code) . '&amp;link=' . urlencode($link) . (isset($_GET ['return']) ? '&amp;return=' . urlencode($_GET ['return']) : null));
$elements = array();
$elements [] = array('type' => 'input_text', 'title' => '<a' . ($dcms->browser_type == 'web' ? ' target="_blank"' : null) . ' href="' . for_value($link) . '">' . __('Ссылка') . '</a>', 'br' => 1, 'info' => array('name' => 'link', 'value' => $link));
$elements [] = array('type' => 'select', 'br' => 1, 'title' => __('Нарушение'), 'info' => array('name' => 'code', 'options' => $codes->options($code)));

if (!$min || $min < 3600) {
    $time = max(round($min / 60), 10);
    $timem = 'm';
} elseif ($min < 86400) {
    $time = max(round($min / 3600), 1);
    $timem = 'h';
} elseif ($min < 2592000) {
    $time = max(round($min / 86400), 1);
    $timem = 'd';
} else {
    $time = max(round($min / 2592000), 1);
    $timem = 'ms';
}

$elements [] = array('type' => 'input_text', 'title' => __('Срок бана') . ' *', 'br' => 0, 'info' => array('size' => 3, 'name' => 'time', 'value' => $time));
$options = array();
$options [] = array('m', __('Минуты'), $timem == 'm');
$options [] = array('h', __('Часы'), $timem == 'h');
$options [] = array('d', __('Сутки'), $timem == 'd');
$options [] = array('ms', __('Месяцы'), $timem == 'ms');
$options [] = array('forever', __('Навсегда'));
$elements [] = array('type' => 'select', 'br' => 1, 'info' => array('name' => 'timem', 'options' => $options));

$elements [] = array('type' => 'checkbox', 'br' => 1, 'info' => array('value' => 1, 'name' => 'full', 'text' => __('Полная блокировка') . ' **'));

$elements [] = array('type' => 'textarea', 'title' => __('Комментарий'), 'br' => 1, 'info' => array('name' => 'comment'));

$elements [] = array('type' => 'text', 'value' => '** ' . __('Блокирует всю навигацию на сайте'), 'br' => 1);
$elements [] = array('type' => 'submit', 'br' => 0, 'info' => array('value' => __('Забанить'), 'name' => 'ban')); // кнопка
$smarty->assign('el', $elements);
$smarty->display('input.form.tpl');
if (isset($_GET ['return'])) {
    $doc->ret(__('Вернуться'), for_value($_GET ['return']));
}
$doc->ret(__('В анкету'), '/profile.view.php?id=' . $ank->id);
$doc->ret(__('Админка'), './');
?>
