<?php

include_once '../sys/inc/start.php';
$doc = new document(4);
$doc->title = __('Рассылка новости');
$doc->ret(__('К новостям'), './');

$id = (int)@$_GET['id'];

$q = mysql_query("SELECT * FROM `news` WHERE `id` = '$id' LIMIT 1");

if (!mysql_num_rows($q))
    $doc->access_denied(__('Новость не найдена или уже удалена'));

$news = mysql_fetch_assoc($q);

$ank = new user($news['id_user']);

if ($ank->group > $user->group)
    $doc->access_denied(__('У Вас нет прав для рассылки данной новости'));
if ($news['sended'])
    $doc->access_denied(__('Новость уже была разослана'));

if (isset($_POST['send'])) {
    if (empty($_POST['captcha']) || empty($_POST['captcha_session']) || !captcha::check($_POST['captcha'], $_POST['captcha_session'])) {
        $doc->err(__('Проверочное число введено неверно'));
    } else {
        $mail_unsubscribe = array();
        $q = mysql_query("SELECT * FROM `mail_unsubscribe`");
        while ($mu = mysql_fetch_assoc($q)) {
            $mail_unsubscribe[$mu['email']] = (bool)$mu['code'];
        }

        $mailes = array();

        $q = mysql_query("SELECT `reg_mail`, `email` FROM `users` ORDER BY `id`");

        while ($um = mysql_fetch_assoc($q)) {
            if ($um['reg_mail']) {
                // по умолчанию отправляем только на регистрационные email`ы
                $mailes[] = $um['reg_mail'];
            } elseif ($um['email'] && !empty($_POST['sendToAnkMail'])) {
                // если регистрационный email отсутствует и разрешено слать на анкетный ящик, то шлем на него
                $mailes[] = $um['email'];
            }
        }

        $mailes_to_send = array();
        for ($i = 0; $i < count($mailes); $i++) {
            if (array_key_exists($mailes[$i], $mail_unsubscribe)) {
                if (!$mail_unsubscribe[$mailes[$i]]) {
                    continue;
                }
            }
            $mailes_to_send[] = $mailes[$i];
        }

        $mailes_to_send = array_unique($mailes_to_send);

        if ($mailes_to_send) {
            $to_unsubscribe_table = array();
            $contents = array();
            for ($i = 0; $i < count($mailes_to_send); $i++) {
                if (array_key_exists($mailes_to_send[$i], $mail_unsubscribe)) {
                    $unsubscribe_code = $mail_unsubscribe[$mailes_to_send[$i]];
                } else {
                    $unsubscribe_code = passgen(32);
                    $to_unsubscribe_table[$mailes_to_send[$i]] = $unsubscribe_code;
                }

                $t = new design();
                $t->assign('title', 'Новости');
                $t->assign('site', $dcms->sitename);
                $t->assign('content', output_text($news['text']));
                $t->assign('email', $mailes_to_send[$i]);
                $t->assign('unsubscribe', 'http://' . $_SERVER['HTTP_HOST'] . '/unsubscribe.php?code=' . $unsubscribe_code);
                $contents[] = $t->fetch('file:' . H . '/sys/templates/mail.news.tpl');
            }
            mail::send($mailes_to_send, $news['title'], $contents);
            mysql_query("UPDATE `news` SET `sended` = '1' WHERE `id` = '$id' LIMIT 1");

            foreach ($to_unsubscribe_table AS $email => $code) {
                mysql_query("INSERT INTO `mail_unsubscribe` (`email`, `code`) VALUES ('" . my_esc($email) . "', '" . my_esc($code) . "')");
            }

            $doc->msg(__('Новость успешно отправлена'));
        } else {
            $doc->err(__('Нет получателей новости'));
        }
        //header('Refresh: 1; url=./');
        exit;
    }
}

$smarty = new design();
$smarty->assign('method', 'post');
$smarty->assign('action', '?id=' . $id . '&amp;' . passgen());
$elements = array();
$elements [] = array('type' => 'checkbox', 'br' => 1, 'info' => array('value' => 1, 'name' => 'sendToAnkMail', 'text' => __('Задействовать анкетный email') . '*'));
$elements [] = array('type' => 'text', 'br' => 1, 'value' => '* ' . __('По-умолчанию рассылка производится только по регистрационным e-mail'));
$elements[] = array('type' => 'captcha', 'session' => captcha::gen(), 'br' => 1);
$elements[] = array('type' => 'submit', 'br' => 0, 'info' => array('name' => 'send', 'value' => __('Разослать новость по Email'))); // кнопка
$smarty->assign('el', $elements);
$smarty->display('input.form.tpl');
