<?php

include_once '../sys/inc/start.php';
dpanel::check_access();
$doc = new document(5);
$doc->title = __('Изменение рекламы');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Refresh: 1; url=adt.php');
    $doc->ret(__('Реклама и баннеры'), 'adt.php');
    $doc->ret(__('Админка'), '/dpanel/');
    $doc->err(__('Ошибка выбора рекламы'));
    exit;
}
$id_adt = (int) $_GET['id'];

$q = mysql_query("SELECT * FROM `advertising` WHERE `id` = '$id_adt'");

if (!mysql_num_rows($q)) {
    header('Refresh: 1; url=adt.php?id=' . $id_adt);
    $doc->ret(__('Вернуться'), 'adt.php?id=' . $id_adt);
    $doc->ret(__('Реклама и баннеры'), 'adt.php');
    $doc->ret(__('Админка'), '/dpanel/');
    $doc->err(__('Рекламная позиция не найдена'));
    exit;
}

$adt = mysql_fetch_assoc($q);

if (isset($_POST['delete'])) {
    if (empty($_POST['captcha']) || empty($_POST['captcha_session']) || !captcha::check($_POST['captcha'], $_POST['captcha_session'])) {
        $doc->err(__('Проверочное число введено неверно'));
    } else {
        mysql_query("DELETE FROM `advertising` WHERE `id` = '$adt[id]'");

        header('Refresh: 1; url=adt.php?id=' . $adt['space']);
        $doc->msg(__('Рекламная площадка успешно удалена'));

        $dcms->log('Реклама', 'Удаление рекламной площадки ' . $adt['name'] . ' (' . $adt['url_link'] . ')');

        $doc->ret(__('Вернуться'), "adt.php?id=$adt[space]");
        $doc->ret(__('Рекламные позиции'), 'adt.php');
        $doc->ret(__('Админка'), '/dpanel/');
        exit;
    }
}

if (isset($_POST['common'])) {
    if (isset($_POST['name'])) {
        $name = text::input_text($_POST['name']);
        if ($name && $name != $adt['name']) {
            $dcms->log('Реклама', 'Изменение названия рекламной площадки ' . $adt['name'] . ' на [url="/dpanel/adt.edit.php?id=' . $id_adt . '"]' . $name . '[/url]');

            $adt['name'] = $name;
            mysql_query("UPDATE `advertising` SET `name` = '" . my_esc($adt['name']) . "' WHERE `id` = '$id_adt' LIMIT 1");

            $doc->msg(__('Название успешно изменено'));
        } elseif (!$name)
            $doc->err(__('Название не может быть пустым'));
    }

    $bold = (int) !empty($_POST['bold']);

    if ($adt['bold'] != $bold) {
        $adt['bold'] = $bold;

        mysql_query("UPDATE `advertising` SET `bold` = '$bold' WHERE `id` = '$id_adt' LIMIT 1");
        if ($adt['bold']) {
            $dcms->log('Реклама', 'Изменение рекламной площадки [url="/dpanel/adt.edit.php?id=' . $id_adt . '"]' . $name . '[/url] (установка жирности)');
            $doc->msg(__('Реклама будет выделяться жирным шрифтом'));
        } else {
            $dcms->log('Реклама', 'Изменение рекламной площадки [url="/dpanel/adt.edit.php?id=' . $id_adt . '"]' . $name . '[/url] (снятие жирности)');
            $doc->msg(__('Реклама не будет выделяться жирным шрифтом'));
        }
    }

    if (isset($_POST['url_link'])) {
        $url_link = text::input_text($_POST['url_link']);
        if ($url_link && $url_link != $adt['url_link']) {
            $adt['url_link'] = $url_link;
            mysql_query("UPDATE `advertising` SET `url_link` = '" . my_esc($adt['url_link']) . "' WHERE `id` = '$id_adt' LIMIT 1");
            $dcms->log('Реклама', 'Изменение рекламной площадки [url="/dpanel/adt.edit.php?id=' . $id_adt . '"]' . $name . '[/url] (ссылка: ' . $adt['url_link'] . ')');
            $doc->msg(__('Адрес ссылки успешно изменен'));
        } elseif (!$url_link)
            $doc->err(__('Адрес ссылки не может быть пуст'));
    }

    if (isset($_POST['url_img'])) {
        $url_img = text::input_text($_POST['url_img']);
        if ($url_img != $adt['url_img']) {
            $adt['url_img'] = $url_img;
            mysql_query("UPDATE `advertising` SET `url_img` = '" . my_esc($adt['url_img']) . "' WHERE `id` = '$id_adt' LIMIT 1");
            $dcms->log('Реклама', 'Изменение рекламной площадки [url="/dpanel/adt.edit.php?id=' . $id_adt . '"]' . $name . '[/url] (адрес изображения: ' . $adt['url_img'] . ')');
            $doc->msg(__('Адрес изображения успешно изменен'));
        }
    }

    $page_main = (int) (isset($_POST['page_main']) && $_POST['page_main']);
    $page_other = (int) (isset($_POST['page_other']) && $_POST['page_other']);

    if (!$page_main && !$page_other)
        $doc->err(__('Реклама должна же где-то отображаться'));
    elseif ($page_main != $adt['page_main'] || $page_other != $adt['page_other']) {
        $adt['page_main'] = $page_main;
        $adt['page_other'] = $page_other;
        mysql_query("UPDATE `advertising` SET `page_main` = '{$adt['page_main']}', `page_other` = '{$adt['page_other']}' WHERE `id` = '$id_adt' LIMIT 1");
        $dcms->log('Реклама', 'Изменение рекламной площадки [url="/dpanel/adt.edit.php?id=' . $id_adt . '"]' . $name . '[/url] (место отображения)');
        $doc->msg(__('Место отображения рекламы изменено'));
    }
}

if (isset($_POST['time'])) {
    $always = (int) (isset($_POST['always']) && $_POST['always']);
    if ($adt['time_end']) {
        if ($always) {
            $adt['time_end'] = 0;
            mysql_query("UPDATE `advertising` SET `time_end` = '0' WHERE `id` = '$id_adt' LIMIT 1");
            $dcms->log('Реклама', 'Изменение рекламной площадки [url="/dpanel/adt.edit.php?id=' . $id_adt . '"]' . $adt['name'] . '[/url] (вечный показ)');
            $doc->msg(__('Вечный показ включен'));
        } else {
            if (isset($_POST['add']) && isset($_POST['mn'])) {
                $add = (int) $_POST['add'];
                $mn = (int) $_POST['mn'];
                // сбрасываем счетчики, если реклама была не активна
                if ($adt['time_start'] && $adt['time_start'] > TIME || $adt['time_end'] && $adt['time_end'] < TIME) {
                    $doc->msg(__('Счетчики показов и переходов сброшены'));
                    $clear_counters_sql = "`count_show_wap` =  '0', `count_out_wap` =  '0', `count_show_pda` =  '0', `count_out_pda` =  '0', `count_show_web` =  '0', `count_out_web` =  '0', ";
                }else
                    $clear_counters_sql = '';

                if ($add && $mn) {
                    if ($adt['time_end'] > TIME)
                        $adt['time_end'] = $adt['time_end'] + $add * $mn * 60 * 60 * 24;
                    else {
                        $adt['time_start'] = TIME;
                        $adt['time_end'] = TIME + $add * $mn * 60 * 60 * 24;
                    }

                    mysql_query("UPDATE `advertising` SET $clear_counters_sql`time_end` = '{$adt['time_end']}', `time_start` = '{$adt['time_start']}' WHERE `id` = '$id_adt' LIMIT 1");
                    $doc->msg(__('Время завершения показа обновлено'));
                }else
                    $doc->err(__('Не корректное время показа'));
            }
        }
    }else {
        if (!$always) {
            $adt['time_end'] = TIME;
            $dcms->log('Реклама', 'Изменение рекламной площадки [url="/dpanel/adt.edit.php?id=' . $id_adt . '"]' . $adt['name'] . '[/url] (вечный показ отключен)');
            mysql_query("UPDATE `advertising` SET `time_end` = '" . TIME . "' WHERE `id` = '$id_adt' LIMIT 1");
            $doc->msg(__('Вечный показ отключен'));
        }
    }
}

if ($adt['time_create'])
    echo __("Позиция создана: %s", vremja($adt['time_create'])) . "<br />\n";

if (!$adt['time_start'])
    echo __("Начало показа неизвестно") . "<br />\n";
elseif ($adt['time_start'] > TIME)
    echo __('Начало показа: %s', vremja($adt['time_start'])) . "<br />\n";
else
    echo __("Показ начался: %s", vremja($adt['time_start'])) . "<br />\n";

if (!$adt['time_end'])
    echo __("Конец показа не обозначен") . "<br />\n";
elseif ($adt['time_end'] > TIME)
    echo __("Конец показа: %s", vremja($adt['time_end'])) . "<br />\n";
else
    echo __("Показ истек: %s", vremja($adt['time_end'])) . "<br />\n";

if (!isset($_GET['delete'])) {
    $form = new design();
    $form->assign('method', 'post');
    $form->assign('action', "?id=$id_adt&amp;" . passgen());
    $elements = array();

    $elements[] = array('type'  => 'input_text', 'title' => __('Название'), 'br'    => 1, 'info'  => array('name'      => 'name', 'value'     => $adt['name']));
    $elements[] = array('type' => 'checkbox', 'br'   => 1, 'info' => array('value'     => 1, 'checked'   => $adt['bold'], 'name'      => 'bold', 'text'      => __('Выделять жирным')));
    $elements[] = array('type'  => 'input_text', 'title' => __('Адрес ссылки'), 'br'    => 1, 'info'  => array('name'      => 'url_link', 'value'     => $adt['url_link']));
    $elements[] = array('type'  => 'input_text', 'title' => __('Адрес изображения'), 'br'    => 1, 'info'  => array('name'  => 'url_img', 'value' => $adt['url_img']));

    $elements[] = array('type' => 'checkbox', 'br'   => 1, 'info' => array('value'     => 1, 'checked'   => $adt['page_main'], 'name'      => 'page_main', 'text'      => __('На главной')));
    $elements[] = array('type' => 'checkbox', 'br'   => 1, 'info' => array('value'   => 1, 'checked' => $adt['page_other'], 'name'    => 'page_other', 'text'    => __('На остальных')));

    $elements[] = array('type' => 'submit', 'br'   => 0, 'info' => array('name'  => 'common', 'value' => __('Применить'))); // кнопка
    $form->assign('el', $elements);
    $form->display('input.form.tpl');

    $form = new design();
    $form->assign('method', 'post');
    $form->assign('action', "?id=$id_adt&amp;" . passgen());
    $elements = array();

    if ($adt['time_end']) {
        $elements[] = array('type'  => 'input_text', 'title' => __('Добавить к времени отображения'), 'br'    => 0, 'info'  => array('size'   => 3, 'name'   => 'add', 'value'  => 1));
        $options = array();
        $options[] = array('1', __('Дней'));
        $options[] = array('7', __('Недель'), 1);
        $options[] = array('31', __('Месяцев'));
        $elements[] = array('type' => 'select', 'br'   => 1, 'info' => array('name'    => 'mn', 'options' => $options));
    }

    $elements[] = array('type' => 'checkbox', 'br'   => 1, 'info' => array('value'   => 1, 'checked' => !$adt['time_end'], 'name'    => 'always', 'text'    => __('Отображать бесконечно')));

    if ($adt['time_start'] && $adt['time_start'] >= TIME || $adt['time_end'] && $adt['time_end'] <= TIME)
        $elements[] = array('type'  => 'text', 'br'    => 1, 'value' => '* ' . __('Счетчики показов и переходов будут сброшены'));

    $elements[] = array('type' => 'submit', 'br'   => 0, 'info' => array('name'  => 'time', 'value' => __('Применить'))); // кнопка
    $form->assign('el', $elements);
    $form->display('input.form.tpl');
}else {
    $form = new design();
    $form->assign('method', 'post');
    $form->assign('action', "?id=$id_adt&amp;delete&amp;" . passgen());
    $elements = array();
    $elements[] = array('type'      => 'captcha', 'session'   => captcha::gen(), 'br'        => 1);
    $elements[] = array('type'      => 'text', 'value'     => __('Подтвердите удаление рекламной позиции'), 'br'        => 1);
    $elements[] = array('type' => 'submit', 'br'   => 0, 'info' => array('name'  => 'delete', 'value' => __('Удалить'))); // кнопка
    $form->assign('el', $elements);
    $form->display('input.form.tpl');
}

$doc->ret(__('Вернуться'), "adt.php?id=$adt[space]");
$doc->ret(__('Рекламные площадки'), 'adt.php');
$doc->ret(__('Админка'), '/dpanel/');
?>