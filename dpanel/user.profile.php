<?php

include_once '../sys/inc/start.php';
dpanel::check_access();
$groups = groups::load_ini();
$doc = new document(4);
$doc->title = __('Профиль');

$browser_types = array('wap', 'pda', 'itouch', 'web');

if (isset($_GET ['id_ank']))
    $ank = new user($_GET ['id_ank']);
else
    $ank = $user;

if (!$ank->group) {
    if (isset($_GET ['return'])) {
        header('Refresh: 1; url=' . $_GET ['return']);
    } else {
        header('Refresh: 1; url=/');
    }

    $doc->err(__('Не удалось загрузить данные пользователя'));
    exit();
}

$doc->title .= ' "' . $ank->login . '"';

if ($ank->group >= $user->group) {
    if (isset($_GET ['return'])) {
        header('Refresh: 1; url=' . $_GET ['return']);
    } else {
        header('Refresh: 1; url=/');
    }

    $doc->err(__('Ваш статус не позволяет производить действия с данным пользователем'));
    exit();
}

if (isset($_POST ['save'])) {
    $ank->realname = text::for_name(@$_POST ['realname']);
    $ank->icq_uin = text::icq_uin(@$_POST ['icq']);
    $ank->balls = abs((int) @$_POST ['balls']);

    if (isset($_POST ['ank_d_r'])) {
        if ($_POST ['ank_d_r'] == null)
            $ank->ank_d_r = null;
        else {
            $ank_d_r = (int) $_POST ['ank_d_r'];
            if ($ank_d_r >= 1 && $ank_d_r <= 31) {
                $ank->ank_d_r = $ank_d_r;
            } else {
                $doc->err(__('Не корректный формат дня рождения'));
            }
        }
    }

    if (isset($_POST ['ank_m_r'])) {
        if ($_POST ['ank_m_r'] == null)
            $ank->ank_m_r = null;
        else {
            $ank_m_r = (int) $_POST ['ank_m_r'];
            if ($ank_m_r >= 1 && $ank_m_r <= 12) {
                $ank->ank_m_r = $ank_m_r;
            } else {
                $doc->err(__('Не корректный формат месяца рождения'));
            }
        }
    }

    if (isset($_POST ['ank_g_r'])) {
        if ($_POST ['ank_g_r'] == null)
            $ank->ank_g_r = null;
        else {
            $ank_g_r = (int) $_POST ['ank_g_r'];
            if ($ank_g_r >= date('Y') - 100 && $ank_g_r <= date('Y')) {
                $ank->ank_g_r = $ank_g_r;
            } else {
                $doc->err(__('Не корректный формат года рождения'));
            }
        }
    }

    if (!empty($_POST ['skype'])) {
        if (!is_valid::skype($_POST ['skype'])) {
            $doc->err(__('Указан не корректный логин Skype'));
        } else {
            $ank->skype = $_POST ['skype'];
        }
    }

    if (!empty($_POST ['email'])) {
        if (!is_valid::mail($_POST ['email'])) {
            $doc->err(__('Указан не корректный E-mail'));
        } else {
            $ank->email = $_POST ['email'];
        }
    }
    if (isset($_POST ['wmid'])) {
        if (empty($_POST ['wmid']))
            $ank->wmid = '';
        elseif (!is_valid::wmid($_POST ['wmid'])) {
            $doc->err(__('Указан не корректный идентификатор WebMoney'));
        } else {
            $ank->wmid = $_POST ['wmid'];
        }
    }
    if (!empty($_POST ['reg_mail'])) {
        if (!is_valid::mail($_POST ['reg_mail'])) {
            $doc->err(__('Указан не корректный Primary E-mail'));
        } else {
            $ank->reg_mail = $_POST ['reg_mail'];
        }
    }

    foreach ($browser_types as $type) {
        $t = "items_per_page_$type";
        // количество пунктов на страницу
        if (!empty($_POST [$t])) {
            $ipp = (int) $_POST [$t];
            if ($ipp >= 5 && $ipp <= 99) {
                $ank->$t = $ipp;
            } else {
                $doc->err(__('Недопустимое количество пунктов на страницу'));
            }
        }

        $t = "theme_$type";
        
        
        
        if (!empty($_POST [$t])) {
        $theme_set = (string) $_POST [$t];
        
        if (themes::exists($theme_set,$type)){
            $ank->$t = $theme_set;
        }        
    }
        
    }
    // временной сдвиг
    if (!empty($_POST ['time_shift'])) {
        $ipp = (int) $_POST ['time_shift'];
        if ($ipp >= - 12 && $ipp <= 12) {
            $ank->time_shift = $ipp;
        } else {
            $doc->err(__('Недопустимое время'));
        }
    }

    $ank->vis_email = (int) !empty($_POST ['vis_email']);
    $ank->vis_icq = (int) !empty($_POST ['vis_icq']);
    $ank->vis_friends = (int) !empty($_POST ['vis_friends']);
    $ank->vis_skype = (int) !empty($_POST ['vis_skype']);

    $dcms->log('Пользователи', 'Изменение профиля пользователя [url=/profile.view.php?id=' . $ank->id . ']' . $ank->login . '[/url]');

    $doc->msg(__('Профиль успешно изменен'));
}

$smarty = new design ();
$smarty->assign('method', 'post');
$smarty->assign('action', "?id_ank=$ank->id&amp;" . passgen() . (isset($_GET ['return']) ? '&amp;return=' . urlencode($_GET ['return']) : null));
$elements = array();

foreach ($browser_types as $type) {
    $t = "items_per_page_$type";
    $elements [] = array('type' => 'input_text', 'title' => __('Пунктов на страницу') . ' (' . $type . ') [5-99]', 'br' => 1, 'info' => array('name' => $t, 'value' => $ank->$t));
}


$options = array(); // темы оформления для wap браузера
$themes_list = themes::getList('wap'); // только для определенного типа браузера
foreach ($themes_list as $theme)
    $options [] = array($theme ['dir'], $theme ['name'], $ank->theme_wap === $theme ['dir']);
$elements [] = array('type' => 'select', 'title' => __('Тема оформления') . ' (WAP)', 'br' => 1, 'info' => array('name' => 'theme_wap', 'options' => $options));

$options = array(); // темы оформления для pda браузера
$themes_list = themes::getList('pda'); // только для определенного типа браузера
foreach ($themes_list as $theme)
    $options [] = array($theme ['dir'], $theme ['name'], $ank->theme_pda === $theme ['dir']);
$elements [] = array('type' => 'select', 'title' => __('Тема оформления') . ' (PDA)', 'br' => 1, 'info' => array('name' => 'theme_pda', 'options' => $options));

$options = array(); // темы оформления для pda браузера
$themes_list = themes::getList('itouch'); // только для определенного типа браузера
foreach ($themes_list as $theme)
    $options [] = array($theme ['dir'], $theme ['name'], $ank->theme_itouch === $theme ['dir']);
$elements [] = array('type' => 'select', 'title' => __('Тема оформления') . ' (I-touch)', 'br' => 1, 'info' => array('name' => 'theme_itouch', 'options' => $options));


$options = array(); // темы оформления для web браузера
$themes_list = themes::getList('web'); // только для определенного типа браузера
foreach ($themes_list as $theme)
    $options [] = array($theme ['dir'], $theme ['name'], $ank->theme_web === $theme ['dir']);
$elements [] = array('type' => 'select', 'title' => __('Тема оформления') . ' (WEB)', 'br' => 1, 'info' => array('name' => 'theme_web', 'options' => $options));


$options = array(); // Врменной сдвиг
for ($i = - 12; $i < 12; $i++)
    $options [] = array($i, date('G:i', TIME + $i * 60 * 60), $ank->time_shift == $i);
$elements [] = array('type' => 'select', 'title' => __('Время'), 'br' => 1, 'info' => array('name' => 'time_shift', 'options' => $options));

$elements [] = array('type' => 'input_text', 'title' => __('Реальное имя'), 'br' => 1, 'info' => array('name' => 'realname', 'value' => $ank->realname));

$elements [] = array('type' => 'text', 'value' => __('Дата рождения').':', 'br' => 1);
$elements [] = array('type' => 'input_text', 'br' => 0, 'info' => array('name' => 'ank_d_r', 'value' => $ank->ank_d_r, 'size' => 2, 'maxlength' => 2));
$elements [] = array('type' => 'input_text', 'br' => 0, 'info' => array('name' => 'ank_m_r', 'value' => $ank->ank_m_r, 'size' => 2, 'maxlength' => 2));
$elements [] = array('type' => 'input_text', 'br' => 1, 'info' => array('name' => 'ank_g_r', 'value' => $ank->ank_g_r, 'size' => 4, 'maxlength' => 4));

$elements [] = array('type' => 'input_text', 'br' => 1, 'title' => __('Баллы'), 'info' => array('name' => 'balls', 'value' => $ank->balls));

$elements [] = array('type' => 'input_text', 'title' => __('Номер ICQ'), 'br' => 1, 'info' => array('name' => 'icq', 'value' => $ank->icq_uin));

$elements [] = array('type' => 'checkbox', 'br' => 1, 'info' => array('value' => 1, 'checked' => $ank->vis_icq, 'name' => 'vis_icq', 'text' => __('Показывать ICQ в анкете')));

$elements [] = array('type' => 'input_text', 'title' => __('Skype логин'), 'br' => 1, 'info' => array('name' => 'skype', 'value' => $ank->skype));

$elements [] = array('type' => 'checkbox', 'br' => 1, 'info' => array('value' => 1, 'checked' => $ank->vis_skype, 'name' => 'vis_skype', 'text' => __('Показывать Skype в анкете')));

$elements [] = array('type' => 'input_text', 'title' => __('Primary E-mail'), 'br' => 1, 'info' => array('name' => 'reg_mail', 'value' => $ank->reg_mail));

$elements [] = array('type' => 'input_text', 'title' => __('E-mail'), 'br' => 1, 'info' => array('name' => 'email', 'value' => $ank->email));
$elements [] = array('type' => 'checkbox', 'br' => 1, 'info' => array('value' => 1, 'checked' => $ank->vis_email, 'name' => 'vis_email', 'text' => __('Показывать Email в анкете')));
$elements [] = array('type' => 'input_text', 'title' => __('WebMoney ID'), 'br' => 1, 'info' => array('name' => 'wmid', 'value' => $ank->wmid));


$elements [] = array('type' => 'checkbox', 'br' => 1, 'info' => array('value' => 1, 'checked' => $ank->vis_friends, 'name' => 'vis_friends', 'text' => __('Отображать список друзей')));

$elements [] = array('type' => 'submit', 'br' => 0, 'info' => array('name' => 'save', 'value' => __('Принять изменения'))); // кнопка
$smarty->assign('el', $elements);
$smarty->display('input.form.tpl');

$doc->ret(__('Действия'), 'user.actions.php?id=' . $ank->id);
$doc->ret(__('Анкета "%s"', $ank->login), '/profile.view.php?id=' . $ank->id);
$doc->ret(__('Админка'), '/dpanel/');
?>
