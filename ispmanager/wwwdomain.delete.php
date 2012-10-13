<?php

include_once '../sys/inc/start.php';
$doc = new document();
$doc->title = 'Удаление WWW - домена';
$isp = new ispmanager();
include_once 'inc/check_login.php';
$doc->ret('Список доменов', 'wwwdomain.php');
$doc->ret('Панель управления', 'index.php');

$list = $isp->getData('wwwdomain');
$domains = array();
foreach ($list->elem as $elem) {
    $domains[] = $elem->name;
}

if (empty($_GET['elid'])) {
    $doc->err('Не выбран домен для редактирования');
    exit;
} elseif (!in_array($_GET['elid'], $domains)) {
    $doc->err('Выбран несуществующий домен');
    exit;
}
$elid = $_GET['elid'];


if (isset($_POST['delete'])) {
    $data = array();
    
    $data['elid'] = $elid;
    $data['maildomain'] = @$_POST['maildomain'];
    $data['dns'] = @$_POST['dns'];
    $data['docroot'] = @$_POST['docroot'];
    $data['extop'] = 'on';
    $data['sok'] = 'ok';

    $result = $isp->getData('wwwdomain.delete', $data);
//echo '<pre>' . print_r($result, true) . '</pre>';
    if ($result->ok) {
        $doc->msg('Домен успешно удален');
        $listing = new listing();
        $post = $listing->post();
        $post->icon('www');
        $post->url = 'wwwdomain.php';
        $post->title = 'Список доменов';
        exit;
    } else {
        $doc->err('Не удалось удалить домен');
    }
}

$form = new design();
$form->assign('method', 'post');
$form->assign('action', '?' . passgen() . '&amp;elid=' . $elid);
$elements [] = array('type' => 'checkbox', 'br' => 1, 'info' => array('value' => 'on', 'name' => 'maildomain', 'text' => __('Удалить почтовый домен')));
$elements [] = array('type' => 'checkbox', 'br' => 1, 'info' => array('value' => 'on', 'name' => 'dns', 'text' => __('Сохранить записи в DNS')));
$elements [] = array('type' => 'checkbox', 'br' => 1, 'info' => array('value' => 'on', 'name' => 'docroot', 'text' => __('Сохранить каталоги и файлы')));
$elements[] = array('type' => 'submit', 'br' => 0, 'info' => array('name' => 'delete', 'value' => __('Удалить домен %s', $elid))); // кнопка
$form->assign('el', $elements);
$form->display('input.form.tpl');
?>
