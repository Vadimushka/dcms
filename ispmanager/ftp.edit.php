<?php

include_once '../sys/inc/start.php';
$doc = new document();
$doc->title = 'FTP - аккаунт';
$isp = new ispmanager();
include_once 'inc/check_login.php';
$doc->ret('FTP - аккаунты', 'ftp.php');
$doc->ret('Панель управления', 'index.php');

$list = $isp->getData('ftp');
$domains = array();
foreach ($list->elem as $elem) {
    $domains[] = $elem->name;
}


if (empty($_GET['elid'])) {
    $doc->err('Не выбран аккаунт для редактирования');
    exit;
} elseif (!in_array($_GET['elid'], $domains)) {
    $doc->err('Выбран несуществующий аккаунт');
    exit;
}
$elid = $_GET['elid'];

$parser = new html_parser($isp->getData('ftp.edit', array('elid' => $elid, 'out' => 'html')));

//echo '<pre>' . print_r($list, true) . '</pre>';
if (isset($_POST['edit'])) {
    $data = array();
    $data['name'] = @$_POST['name'];
    $data['password'] = @$_POST['password'];
    $data['confirm'] = @$_POST['confirm'];
    $data['note'] = @$_POST['note'];
    $data['elid'] = $elid;
    $data['sok'] = 'ok';

    $result = $isp->getData('ftp.edit', $data);
    echo '<pre>' . print_r($result, true) . '</pre>';
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
$form->assign('action', '?' . passgen() . '&amp;elid=' . $elid);

$elements = array();
$elements[] = array('type' => 'input_text', 'title' => __('Имя пользователя'), 'br' => 1, 'info' => array('name' => 'name', 'value' => $parser->getValue('name')));
$elements[] = array('type' => 'password', 'title' => __('Новый пароль'), 'br' => 1, 'info' => array('name' => 'password', 'value' => $parser->getValue('password')));
$elements[] = array('type' => 'password', 'title' => __('Подтверждение пароля'), 'br' => 1, 'info' => array('name' => 'confirm', 'value' => $parser->getValue('confirm')));
$elements[] = array('type' => 'textarea', 'title' => __('Заметки'), 'br' => 1, 'info' => array('name' => 'note', 'value' => $parser->getValue('note')));

$elements[] = array('type' => 'submit', 'br' => 0, 'info' => array('name' => 'edit', 'value' => __('Применить'))); // кнопка
$form->assign('el', $elements);
$form->display('input.form.tpl');
?>
