<?php

include_once '../sys/inc/start.php';
$doc = new document();
$doc->title = 'Создание FTP - аккаунта';
$isp = new ispmanager();
include_once 'inc/check_login.php';
$doc->ret('FTP - аккаунты', 'ftp.php');
$doc->ret('Панель управления', 'index.php');

$parser = new html_parser($isp->getData('ftp.edit', array('out' => 'html')));

//echo '<pre>' . print_r($list, true) . '</pre>';
if (isset($_POST['edit'])) {
    $data = array();
    $data['name'] = @$_POST['name'];
    $data['passwd'] = @$_POST['passwd'];
    $data['confirm'] = @$_POST['confirm'];
    $data['htype'] = @$_POST['htype'];
    $data['domain'] = @$_POST['domain'];
    $data['dir'] = @$_POST['dir'];
    $data['note'] = @$_POST['note'];
    $data['elid'] = '';
    $data['sok'] = 'ok';

    $result = $isp->getData('ftp.edit', $data);
    //echo '<pre>' . print_r($result, true) . '</pre>';
    if ($result->ok) {
        $doc->msg('Изменения успешно приняты');
        $listing = new listing();
        $post = $listing->post();
        $post->icon('ftp');
        $post->url = 'ftp.php';
        $post->title = 'FTP - аккаунты';
        exit;
    } else {
        if ($result->error) {
            $doc->err(__('Поле %s не заполнено', $result->error['val']));
        } else {
            $doc->err('Не удалось изменить аккаунт');
        }
    }
}


$form = new design();
$form->assign('method', 'post');
$form->assign('action', '?' . passgen());

$elements = array();
$elements[] = array('type' => 'input_text', 'title' => __('Имя пользователя'), 'br' => 1, 'info' => array('name' => 'name', 'value' => $parser->getValue('name')));
$elements[] = array('type' => 'password', 'title' => __('Новый пароль'), 'br' => 1, 'info' => array('name' => 'passwd', 'value' => $parser->getValue('passwd')));
$elements[] = array('type' => 'password', 'title' => __('Подтверждение пароля'), 'br' => 1, 'info' => array('name' => 'confirm', 'value' => $parser->getValue('confirm')));

if ($parser->hasSelect('htype')) {
    $options = array();
    if ($parser->hasSelectOption('htype', 'equalme'))
        $options[] = array('equalme', __('Эквивалент администратора'), $parser->getValue('htype') == 'equalme');

    if ($parser->hasSelectOption('htype', 'docroot'))
        $options[] = array('docroot', __('Директория WWW - домена'), $parser->getValue('htype') == 'docroot');

    if ($parser->hasSelectOption('htype', 'custom'))
        $options[] = array('custom', __('Другая директория'), $parser->getValue('htype') == 'custom');

    $elements[] = array('type' => 'select', 'title' => __('Домашняя директория'), 'br' => 1, 'info' => array('name' => 'htype', 'options' => $options));
}

$elements[] = array('type' => 'select', 'title' => __('WWW - домен'), 'br' => 1, 'info' => array('name' => 'domain', 'options' => $parser->getSelectOptions('domain')));
$elements[] = array('type' => 'input_text', 'title' => __('Другая директория'), 'br' => 1, 'info' => array('name' => 'dir'));

$elements[] = array('type' => 'textarea', 'title' => __('Заметки'), 'br' => 1, 'info' => array('name' => 'note', 'value' => $parser->getValue('note')));

$elements[] = array('type' => 'submit', 'br' => 0, 'info' => array('name' => 'edit', 'value' => __('Применить'))); // кнопка
$form->assign('el', $elements);
$form->display('input.form.tpl');
?>
