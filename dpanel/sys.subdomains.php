<?php

include_once '../sys/inc/start.php';
dpanel::check_access();
$doc = new document(groups::max());
$doc->title = __('Поддомены');

if (!$dcms->check_domain_work) {
    $dcms->check_domain_work = passgen();
}

function domain_check($domain) {
    global $dcms;
    $http = new http_client('http://' . $domain . '/?check_domain_work');
    $http->timeout = 10;
    $content = $http->getContent();
    if ($dcms->check_domain_work === $content) {
        return true;
    } else {
        return false;
    }
}

if (isset($_POST ['save'])) {

    $subdomain_theme_redirect_old = $dcms->subdomain_theme_redirect;
    $dcms->subdomain_theme_redirect = (int) !empty($_POST ['subdomain_theme_redirect']);
    $dcms->subdomain_replace_url = (int) !empty($_POST ['subdomain_replace_url']);

    $subdomain_wap_enable_old = $dcms->subdomain_wap_enable;
    $dcms->subdomain_wap_enable = (int) !empty($_POST ['subdomain_wap_enable']);

    $subdomain_pda_enable_old = $dcms->subdomain_pda_enable;
    $dcms->subdomain_pda_enable = (int) !empty($_POST ['subdomain_pda_enable']);

    $subdomain_itouch_enable_old = $dcms->subdomain_itouch_enable;
    $dcms->subdomain_itouch_enable = (int) !empty($_POST ['subdomain_itouch_enable']);

    $subdomain_web_enable_old = $dcms->subdomain_web_enable;
    $dcms->subdomain_web_enable = (int) !empty($_POST ['subdomain_web_enable']);


    $subdomain_main_old = $dcms->subdomain_main;
    $dcms->subdomain_main = text::input_text($_POST ['subdomain_main']);

    $subdomain_wap_old = $dcms->subdomain_wap;
    $dcms->subdomain_wap = text::input_text($_POST ['subdomain_wap']);

    $subdomain_pda_old = $dcms->subdomain_pda;
    $dcms->subdomain_pda = text::input_text($_POST ['subdomain_pda']);

    $subdomain_itouch_old = $dcms->subdomain_itouch;
    $dcms->subdomain_itouch = text::input_text($_POST ['subdomain_itouch']);


    $subdomain_web_old = $dcms->subdomain_web;
    $dcms->subdomain_web = text::input_text($_POST ['subdomain_web']);





    if ($dcms->subdomain_theme_redirect && $dcms->subdomain_theme_redirect != $subdomain_theme_redirect_old) {
        if (!$dcms->subdomain_main) {
            $doc->err(__('Основной домен не введен'));
            $dcms->subdomain_theme_redirect = 0;
        } elseif (!domain_check($dcms->subdomain_main)) {
            $doc->err(__('Основной домен не открывает данный сайт'));
            $dcms->subdomain_theme_redirect = 0;
        }
    }


    if ($dcms->subdomain_wap_enable && ($dcms->subdomain_wap_enable != $subdomain_wap_enable_old || $subdomain_wap_old != $dcms->subdomain_wap )) {
        if (!$dcms->subdomain_wap) {
            $doc->err(__('Поддомен для WAP тем оформления не задан'));
            $dcms->subdomain_wap_enable = 0;
        } elseif (!domain_check($dcms->subdomain_wap . '.' . $dcms->subdomain_main)) {
            $doc->err(__('Поддомен для WAP тем оформления не открывает данный сайт'));
            $dcms->subdomain_wap_enable = 0;
        }
    }

    if ($dcms->subdomain_pda_enable && ( $dcms->subdomain_pda_enable != $subdomain_pda_enable_old || $subdomain_pda_old != $dcms->subdomain_pda )) {
        if (!$dcms->subdomain_pda) {
            $doc->err(__('Поддомен для PDA тем оформления не задан'));
            $dcms->subdomain_pda_enable = 0;
        } elseif (!domain_check($dcms->subdomain_pda . '.' . $dcms->subdomain_main)) {
            $doc->err(__('Поддомен для PDA тем оформления не открывает данный сайт'));
            $dcms->subdomain_pda_enable = 0;
        }
    }
    if ($dcms->subdomain_itouch_enable && ($dcms->subdomain_itouch_enable != $subdomain_itouch_enable_old || $subdomain_itouch_old != $dcms->subdomain_itouch )) {
        if (!$dcms->subdomain_itouch) {
            $doc->err(__('Поддомен для iTouch тем оформления не задан'));
            $dcms->subdomain_itouch_enable = 0;
        } elseif (!domain_check($dcms->subdomain_itouch . '.' . $dcms->subdomain_main)) {
            $doc->err(__('Поддомен для iTouch тем оформления не открывает данный сайт'));
            $dcms->subdomain_itouch_enable = 0;
        }
    }
    
    if ($dcms->subdomain_web_enable && ($dcms->subdomain_web_enable != $subdomain_web_enable_old || $subdomain_web_old != $dcms->subdomain_web )) {
        if (!$dcms->subdomain_web) {
            $doc->err(__('Поддомен для WEB тем оформления не задан'));
            $dcms->subdomain_web_enable = 0;
        } elseif (!domain_check($dcms->subdomain_web . '.' . $dcms->subdomain_main)) {
            $doc->err(__('Поддомен для WEB тем оформления не открывает данный сайт'));
            $dcms->subdomain_web_enable = 0;
        }
    }


    if ($dcms->save_settings()) {
        $doc->msg(__('Настройки успешно сохранены'));
    } else {
        $doc->err(__('Нет прав на запись в файл настроек'));
    }
}



$form = new design ();
$form->assign('method', 'post');
$form->assign('action', '?' . passgen());
$elements = array();


$elements [] = array('type' => 'input_text', 'title' => __('Основной домен'), 'br' => 1, 'info' => array('name' => 'subdomain_main', 'value' => $dcms->subdomain_main));
$elements [] = array('type' => 'checkbox', 'br' => 1, 'info' => array('value' => 1, 'checked' => $dcms->subdomain_theme_redirect, 'name' => 'subdomain_theme_redirect', 'text' => __('При переходе на главный домен переадресовывать на поддомен в соответствии с автоматически определенным типом браузера')));
$elements [] = array('type' => 'checkbox', 'br' => 1, 'info' => array('value' => 1, 'checked' => $dcms->subdomain_replace_url, 'name' => 'subdomain_replace_url', 'text' => __('Удалять поддомен из ссылок')));



$elements [] = array('type' => 'input_text', 'title' => __('Поддомен WAP (*.%s)', $dcms->subdomain_main), 'br' => 1, 'info' => array('name' => 'subdomain_wap', 'value' => $dcms->subdomain_wap));
$elements [] = array('type' => 'checkbox', 'br' => 1, 'info' => array('value' => 1, 'checked' => $dcms->subdomain_wap_enable, 'name' => 'subdomain_wap_enable', 'text' => __('Выбирать WAP тему при переходе по данному поддомену')));

$elements [] = array('type' => 'input_text', 'title' => __('Поддомен PDA (*.%s)', $dcms->subdomain_main), 'br' => 1, 'info' => array('name' => 'subdomain_pda', 'value' => $dcms->subdomain_pda));
$elements [] = array('type' => 'checkbox', 'br' => 1, 'info' => array('value' => 1, 'checked' => $dcms->subdomain_pda_enable, 'name' => 'subdomain_pda_enable', 'text' => __('Выбирать PDA тему при переходе по данному поддомену')));

$elements [] = array('type' => 'input_text', 'title' => __('Поддомен iTouch (*.%s)', $dcms->subdomain_main), 'br' => 1, 'info' => array('name' => 'subdomain_itouch', 'value' => $dcms->subdomain_itouch));
$elements [] = array('type' => 'checkbox', 'br' => 1, 'info' => array('value' => 1, 'checked' => $dcms->subdomain_itouch_enable, 'name' => 'subdomain_itouch_enable', 'text' => __('Выбирать iTouch тему при переходе по данному поддомену')));

$elements [] = array('type' => 'input_text', 'title' => __('Поддомен WWW (*.%s)', $dcms->subdomain_main), 'br' => 1, 'info' => array('name' => 'subdomain_web', 'value' => $dcms->subdomain_web));
$elements [] = array('type' => 'checkbox', 'br' => 1, 'info' => array('value' => 1, 'checked' => $dcms->subdomain_web_enable, 'name' => 'subdomain_web_enable', 'text' => __('Выбирать WEB тему при переходе по данному поддомену')));


$elements [] = array('type' => 'submit', 'br' => 0, 'info' => array('name' => 'save', 'value' => __('Применить'))); // кнопка
$form->assign('el', $elements);
$form->display('input.form.tpl');
?>
