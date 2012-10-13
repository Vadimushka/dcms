<?php

include_once '../sys/inc/start.php';
include_once '../sys/inc/update.php';
dpanel::check_access();
$doc = new document(groups::max());

$skips = ini::read(H . '/sys/ini/update_skips.ini', true);

$doc->title = __('Обновление системы');

if (!@function_exists('ignore_user_abort')) {
    $doc->err(__('функция ignore_user_abort не поддерживается данным сервером. Обновление невозможно.'));
    $doc->ret(__('Админка'), '/dpanel/');
    exit;
}

if (!@function_exists('set_time_limit')) {
    $doc->err(__('функция set_time_limit не поддерживается данным сервером. Обновление невозможно.'));
    $doc->ret(__('Админка'), '/dpanel/');
    exit;
}

$update = new update();

if (($ver = $update->is_updateble()) !== false) {
    $list = $update->getUpdatebleFiles();

    $can_skip = array();
    foreach ($list as $file) {
        if (isset($skips[$file]['name'])) {
            $can_skip[$file] = $skips[$file]['name'];
        }
    }


    if (!empty($_POST['update'])) {
        $skip = array();
        foreach ($can_skip as $key => $value) {
            if (empty($_POST[$key])) {
                $skip[] = $key;
            }
        }

        $update->setSkipFiles($skip);
        if ($update->start()) {
            $doc->msg(__('Обновление успешно завершено'));
            $doc->ret(__('Вернуться'), '?');
            $doc->ret(__('Админка'), '/dpanel/');
            exit;
        } else {
            $doc->err(__('При обновлении возникли ошибки'));
        }
    }






    $doc->msg(__('Доступно обновление: %s > %s', $dcms->version, $ver));



    $form = new design ();
    $form->assign('method', 'post');
    $form->assign('action', '?' . passgen());
    $elements = array();
    foreach ($can_skip as $key => $value) {
        $elements [] = array('type' => 'checkbox', 'br' => 1, 'info' => array('value' => 1, 'checked' => 1, 'name' => $key, 'text' => $value));
    }

    $elements [] = array('type' => 'textarea', 'title' => __('Полный список обновляемых файлов'), 'br' => 1, 'info' => array('value' => implode("\r\n", $list)));

    $elements [] = array('type' => 'submit', 'br' => 0, 'info' => array('name' => 'update', 'value' => __('Обновить'))); // кнопка
    $form->assign('el', $elements);
    $form->display('input.form.tpl');
} else {
    $doc->msg(__('Вы используете последнюю версию: %s', $dcms->version));
}



$doc->ret(__('Админка'), '/dpanel/');
?>