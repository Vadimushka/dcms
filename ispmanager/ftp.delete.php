<?php

include_once '../sys/inc/start.php';
$doc = new document();
$doc->title = 'Удаление FTP - аккаунта';
$isp = new ispmanager();
include_once 'inc/check_login.php';
$doc->ret('FTP - аккаунты', 'ftp.php');
$doc->ret('Панель управления', 'index.php');

$list = $isp->getData('ftp');
$ftps = array();
foreach ($list->elem as $elem) {
    $ftps[] = $elem->name;
}

if (empty($_GET['elid'])) {
    $doc->err('Не выбран аккаунт для удаления');
    exit;
} elseif (!in_array($_GET['elid'], $ftps)) {
    $doc->err('Выбран несуществующий аккаунт');
    exit;
}
$elid = $_GET['elid'];


if (isset($_POST['delete'])) {
    $data = array();

    $data['elid'] = $elid;

    $result = $isp->getData('ftp.delete', $data);
//echo '<pre>' . print_r($result, true) . '</pre>';
    if ($result->ok) {
        $doc->msg('Аккаунт успешно удален');
        $listing = new listing();
        $post = $listing->post();
        $post->icon('ftp');
        $post->url = 'ftp.php';
        $post->title = 'FTP - аккаунты';
        exit;
    } else {
        $doc->err('Не удалось удалить аккаунт');
    }
}

$form = new design();
$form->assign('method', 'post');
$form->assign('action', '?' . passgen() . '&amp;elid=' . $elid);
$elements[] = array('type' => 'submit', 'br' => 0, 'info' => array('name' => 'delete', 'value' => __('Удалить аккаунт %s', $elid))); // кнопка
$form->assign('el', $elements);
$form->display('input.form.tpl');
?>
