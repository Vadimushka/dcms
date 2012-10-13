<?php

include_once 'sys/inc/start.php';
$doc = new document (); // инициализация документа для браузера
$doc->title = __('Анкета');

if (isset($_GET ['id'])) {
    $ank = new user((int) $_GET ['id']);
} else {
    $ank = $user;
}

if (!$ank->group)
    $doc->access_denied(__('Нет данных'));

if ($user->id && $ank->id == $user->id)
    $doc->title = __('Моя анкета');
else
    $doc->title = __('Анкета "%s"', $ank->login);

$doc->description = __('Анкета "%s"', $ank->login);
$doc->keywords [] = $ank->login;

if ($user->group && $ank->id && $user->id != $ank->id && isset($_GET ['friend'])) {
    // обработка действий с "другом"
    $q = mysql_query("SELECT * FROM `friends` WHERE `id_user` = '$user->id' AND `id_friend` = '$ank->id' LIMIT 1");
    if (mysql_num_rows($q)) {
        $friend = mysql_fetch_assoc($q);
        if ($friend ['confirm']) {
            // если Вы уже являетель другом
            if (isset($_POST ['delete'])) {
                // удаляем пользователя из друзей
                mysql_query("DELETE FROM `friends` WHERE `id_user` = '{$user->id}' AND `id_friend` = '{$ank->id}' OR `id_user` = '{$ank->id}' AND `id_friend` = '{$user->id}'");
                $doc->msg(__('Пользователь успешно удален из друзей'));
            }
        } else {
            // если не являетесь другом
            if (isset($_POST ['no'])) {
                // не принимаем предложение дружбы
                mysql_query("DELETE FROM `friends` WHERE `id_user` = '$user->id' AND `id_friend` = '$ank->id' OR `id_user` = '$ank->id' AND `id_friend` = '$user->id'");
                mysql_query("UPDATE `users` SET `friend_new_count` = `friend_new_count` - '1' WHERE `id` = '{$user->id}' LIMIT 1");

                $doc->msg(__('Предложение дружбы отклонено'));
            } elseif (isset($_POST ['ok'])) {
                // принимаем предложение дружбы
                mysql_query("UPDATE `friends` SET `confirm` = '1' WHERE `id_user` = '$user->id' AND `id_friend` = '$ank->id' LIMIT 1");
                mysql_query("UPDATE `users` SET `friend_new_count` = `friend_new_count` - '1' WHERE `id` = '{$user->id}' LIMIT 1");
                // на всякий случай пытаемся добавить поле (хотя оно уже должно быть), если оно уже есть, то дублироваться не будет
                mysql_query("INSERT INTO `friends` (`confirm`, `id_user`, `id_friend`) VALUES ('1', '$ank->id', '$user->id')");
                $doc->msg(__('Предложение дружбы принято'));
            }
        }
    } else {
        if (isset($_GET ['friend']) && isset($_POST ['add'])) {
            // предлагаем дружбу
            // отметка о запросе дружбы
            mysql_query("INSERT INTO `friends` (`confirm`, `id_user`, `id_friend`) VALUES ('0', '$ank->id', '$user->id'), ('1', '$user->id', '$ank->id')");
            mysql_query("UPDATE `users` SET `friend_new_count` = `friend_new_count` + '1' WHERE `id` = '{$ank->id}' LIMIT 1");

            $doc->msg(__('Предложение дружбы успешно отправлено'));
        }
    }
}

if ($ank->is_ban) {
    $posts = array();
    $q = mysql_query("SELECT * FROM `ban` WHERE `id_user` = '$ank->id' AND `time_start` < '" . TIME . "' AND (`time_end` is NULL OR `time_end` > '" . TIME . "') ORDER BY `id` DESC");
    while ($c = mysql_fetch_assoc($q)) {
        $adm = new user($c ['id_adm']);

        $t = ($adm->group <= $user->group ? '<a href="/profile.view.php?id=' . $adm->id . '">' . $adm->nick . '</a>: ' : '') . for_value($c ['code']);


        $post = '';
        if ($c ['time_start'] && TIME < $c ['time_start']) {
            $post .= '[b]' . __('Начало действия') . ':[/b]' . vremja($c ['time_start']) . "\n";
        }
        if ($c['time_end'] === NULL) {
            $post .= '[b]' . __('Пожизненная блокировка') . "[/b]\n";
        } elseif (TIME < $c['time_end']) {
            $post .= __('Осталось: %s', vremja($c['time_end'])) . "\n";
        }
        if ($c['link']) {
            $post .= __('Ссылка на нарушение: %s', $c['link']) . "\n";
        }

        $post .= __('Комментарий: %s', $c['comment']) . "\n";


        $posts [] = array('title' => $t, 'post' => output_text($post));
    }
    $show = new design ();
    $show->assign('post', $posts);
    $show->display('design.listing.tpl');
}




// Аватар 
if ($path = $ank->getAvatar($doc->img_max_width())) {
    echo "<img class='photo' src='" . $path . "' alt='" . __('Аватар %s', $ank->login) . "' /><br />\n";
}







// статус
if ($ank->group > 1) {
    echo "<b>$ank->group_name</b>";

    $q = mysql_query("SELECT `id_adm` FROM `log_of_user_status` WHERE `id_user` = '$ank->id' ORDER BY `id` DESC LIMIT 1");
    if (mysql_num_rows($q)) {
        $adm = new user(mysql_result($q, 0));
        echo ' (' . __('Назначил' . ($adm->sex ? '' : 'а')) . ' ' . __($adm->group_name) . ' "' . $adm->nick . '")';
    }
    echo "<br />\n";
} // VIP статус
elseif ($ank->is_vip) {
    echo '<img src="/sys/images/icons/vip.png" alt="VIP" /> VIP <a href="/faq.php?info=vip&amp;return=' . URL . '">?</a><br />';
}

// реальное имя
if ($ank->realname) {
    echo __('Имя') . ": {$ank->realname}<br />\n";
}
// дата рождения и возраст
if ($ank->ank_d_r && $ank->ank_m_r && $ank->ank_g_r) {
    echo __('Дата рождения') . ': ' . $ank->ank_d_r . ' ' . rus_mes($ank->ank_m_r) . ' ' . $ank->ank_g_r . "<br />\n";
    echo __('Возраст') . ': ' . misc::get_age($ank->ank_g_r, $ank->ank_m_r, $ank->ank_d_r, true) . "<br />\n";
} elseif ($ank->ank_d_r && $ank->ank_m_r)
    echo __('День рождения') . ': ' . $ank->ank_d_r . ' ' . rus_mes($ank->ank_m_r) . "<br />\n";



if ($ank->id) {

// папка фотоальбомов пользователей
    $photos = new files(FILES . '/.photos');
// папка альбомов пользователя
    $albums_path = FILES . '/.photos/' . $ank->id;

    if (!@is_dir($albums_path)) {
        if ($albums_dir = $photos->mkdir($ank->login, $ank->id)) {
            $albums_dir->group_show = 0;
            $albums_dir->group_write = min($ank->group, 2);
            $albums_dir->group_edit = max($ank->group, 4);
            $albums_dir->id_user = $ank->id;
            unset($albums_dir);
        }
    }

    $albums_dir = new files($albums_path);

    $photos_count ['all'] = $albums_dir->count();
    if ($photos_count ['all']) {
        $photos_count ['new'] = $albums_dir->count(NEW_TIME);

        echo '<img src="/sys/images/icons/photos.png" alt="" /><a href="/photos/albums.php?id=' . $ank->id . '">' . __('Фотографии') . '</a> (' . $photos_count ['all'] . ')' . ($photos_count ['new'] ? ' +' . $photos_count ['new'] : null) . '<br />';
    }
}


// аська
if ($ank->icq_uin) {
    if ($ank->is_friend($user) || $ank->vis_icq) {
        echo "<img src='http://wwp.icq.com/scripts/online.dll?icq={$ank->icq_uin}&amp;img=27' alt='" . __('ICQ UIN') . "' /> {$ank->icq_uin}<br />";
    } else
        echo __('ICQ UIN') . ': <a href="/faq.php?info=hide&amp;return=' . URL . '">?</a><br />';
}
// аська
if ($ank->skype) {
    if ($ank->is_friend($user) || $ank->vis_skype)
        echo "<img src=\"http://mystatus.skype.com/smallicon/{$ank->skype}\" width=\"16\" height=\"16\" alt=\"" . __("Мой статус") . "\" /> <a href=\"skype:{$ank->skype}?chat\">{$ank->skype}</a><br />";
    else
        echo __('Skype') . ': <a href="/faq.php?info=hide&amp;return=' . URL . '">?</a><br />';
}
// мыло
if ($ank->email) {
    $doc->keywords [] = $ank->email;

    if ($ank->is_friend($user) || $ank->vis_email) {
        if (preg_match("#\@(mail|bk|inbox|list)\.ru$#i", $ank->email))
            echo "<img src='http://status.mail.ru/?{$ank->email}' width='13' height='13' alt='' /> <a href='mailto:{$ank->email}'>{$ank->email}</a><br />";
        else
            echo __("E-mail") . ": <a href='mailto:{$ank->email}'>{$ank->email}</a><br />";
    } else
        echo __('E-mail') . ': <a href="/faq.php?info=hide&amp;return=' . URL . '">?</a><br />';
}
// Регистрационный email
if ($ank->reg_mail) {
    if ($user->group > $ank->group) {
        echo __("Регистрационный E-mail") . ": <a href='mailto:{$ank->reg_mail}'>{$ank->reg_mail}</a><br />";
    }
}

if ($ank->wmid) {
    echo __("WMID") . ": <a" . ($dcms->browser_type == 'web' ? " target='_blank'" : null) . " href='http://passport.webmoney.ru/asp/certview.asp?wmid=$ank->wmid'>$ank->wmid</a> BL:<img src=\"http://stats.wmtransfer.com/Levels/pWMIDLevel.aspx?wmid=$ank->wmid&amp;w=35&amp;h=16\" width=\"35\" height=\"16\" alt=\"BL\" /><br />";
}

if ($ank->is_friend($user) || $ank->vis_friends) {
    $k_friends = mysql_result(mysql_query("SELECT COUNT(*) FROM `friends` WHERE `id_user` = '$ank->id' AND `confirm` = '1'"), 0);
    echo "<a href='" . ($ank->id == $user->id ? "/my.friends.php" : "/profile.friends.php?id={$ank->id}") . "'>" . __('Друзья') . ": " . $k_friends . '</a><br />';
} else
    echo __('Друзья') . ': <a href="/faq.php?info=hide&amp;return=' . URL . '">?</a><br />';

echo "<a href='/profile.reviews.php?id={$ank->id}'>" . __('Рейтинг') . ": " . $ank->rating . '</a><br />';

echo __("Баллы") . ": {$ank->balls}<br />";

if ($ank->description)
    echo __('О себе') . ': ' . output_text($ank->description) . "<br />";

echo __('Последний визит') . ': ' . vremja($ank->last_visit) . '<br />';
echo __("Всего переходов") . ": {$ank->conversions}<br />";
echo __('Дата регистрации') . ': ' . date('d-m-Y', $ank->reg_date) . '<br />';

$q = mysql_query("SELECT `id_user` FROM `invations` WHERE `id_invite` = '$ank->id' LIMIT 1");
if (mysql_num_rows($q)) {
    $inv = new user(mysql_result($q, 0, 'id_user'));
    echo output_text(__('По приглашению от %s', '[user]' . $inv->id . '[/user]'));
}

if ($user->group && $ank->id && $user->id != $ank->id) {
    $q = mysql_query("SELECT * FROM `friends` WHERE `id_user` = '$user->id' AND `id_friend` = '$ank->id' LIMIT 1");
    if (mysql_num_rows($q)) {
        $friend = mysql_fetch_assoc($q);
        if ($friend ['confirm']) {
            // пользователь находится в друзьях
            if (isset($_GET ['friend']) && $_GET ['friend'] == 'delete') {
                $form = new design ();
                $form->assign('method', 'post');
                $form->assign('action', "?id={$ank->id}&amp;friend&amp;" . passgen());
                $elements = array();
                $elements [] = array('type' => 'text', 'br' => 1, 'value' => output_text(__('Действительно хотите удалить пользователя "%s" из друзей?', $ank->login))); // правила
                $elements [] = array('type' => 'submit', 'br' => 0, 'info' => array('name' => 'delete', 'value' => __('Да, удалить'))); // кнопка
                $form->assign('el', $elements);
                $form->display('input.form.tpl');
            }

            if (!$ank->is_friend($user))
                echo "<b>" . __('Пользователь еще не подтвердил факт Вашей дружбы') . "</b><br />";
            $doc->act(__('Удалить из друзей'), "?id={$ank->id}&amp;friend=delete");
        } else {
            // пользователь не в друзьях
            $form = new design ();
            $form->assign('method', 'post');
            $form->assign('action', "?id={$ank->id}&amp;friend&amp;" . passgen());
            $elements = array();
            $elements [] = array('type' => 'text', 'br' => 1, 'value' => output_text(__('Пользователь "%s" предлагает Вам дружбу', $ank->login))); // правила
            $elements [] = array('type' => 'submit', 'br' => 0, 'info' => array('name' => 'ok', 'value' => __('Принимаю'))); // кнопка
            $elements [] = array('type' => 'submit', 'br' => 0, 'info' => array('name' => 'no', 'value' => __('Не принимаю'))); // кнопка
            $form->assign('el', $elements);
            $form->display('input.form.tpl');
        }
    } else {
        if (isset($_GET ['friend']) && $_GET ['friend'] == 'add') {
            $form = new design ();
            $form->assign('method', 'post');
            $form->assign('action', "?id={$ank->id}&amp;friend&amp;" . passgen());
            $elements = array();
            $elements [] = array('type' => 'text', 'br' => 1, 'value' => output_text(__('Предложить пользователю "%s" дружбу?', $ank->login))); // правила
            $elements [] = array('type' => 'submit', 'br' => 0, 'info' => array('name' => 'add', 'value' => __('Предложить'))); // кнопка
            $form->assign('el', $elements);
            $form->display('input.form.tpl');
        }
        $doc->act(__('Добавить в друзья'), "?id={$ank->id}&amp;friend=add");
    }
}

if ($user->group && $ank->id != $user->id) {
    $doc->act(__('Написать сообщение'), "my.mail.php?id={$ank->id}");

    if ($user->group > $ank->group) {
        $doc->act(__('Доступные действия'), "/dpanel/user.actions.php?id={$ank->id}");
    }
}
if ($user->group)
    $doc->ret(__('Личное меню'), '/menu.user.php');
?>