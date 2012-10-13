<?php

include_once '../sys/inc/start.php';
$doc = new document();
$doc->title = 'Изменение WWW - домена';
$isp = new ispmanager();
include_once 'inc/check_login.php';
$doc->ret('Список доменов', 'wwwdomain.php');
$doc->ret('Панель управления', 'index.php');
$iplist = $isp->getData('iplist');

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

$parser = new html_parser($isp->getData('wwwdomain.edit', array('elid' => $elid, 'out' => 'html')));

//echo '<pre>' . print_r($list, true) . '</pre>';
if (isset($_POST['edit'])) {
    $data = array();
    $data['domain'] = @$_POST['domain'];
    $data['alias'] = preg_replace("/[\n\r\t]+/s", ' ', @$_POST['alias']);
    $data['docroot'] = @$_POST['docroot'];
    $data['ip'] = @$_POST['ip'];
    $data['admin'] = @$_POST['admin'];
    $data['charset'] = @$_POST['charset'];
    $data['index'] = @$_POST['index'];
    $data['autosubdomain'] = @$_POST['autosubdomain'];
    $data['php'] = @$_POST['php'];
    $data['elid'] = $elid;
    $data['sok'] = 'ok';

    $result = $isp->getData('wwwdomain.edit', $data);

    if ($result->ok) {
        $doc->msg('Изменения успешно приняты');
        $listing = new listing();
        $post = $listing->post();
        $post->icon('www');
        $post->url = 'wwwdomain.php';
        $post->title = 'Список доменов';
        exit;
    } else {
        if ($result->error) {
            $doc->err(__('Поле %s не заполнено', $result->error['val']));
        } else {
            $doc->err('Не удалось изменить домен');
        }
    }
}


$form = new design();
$form->assign('method', 'post');
$form->assign('action', '?' . passgen() . '&amp;elid=' . $elid);
$elements = array();
$elements[] = array('type' => 'input_text', 'title' => __('Имя домена'), 'br' => 1, 'info' => array('name' => 'domain', 'value' => $parser->getValue('domain')));
$elements[] = array('type' => 'textarea', 'title' => __('Псевдонимы'), 'br' => 1, 'info' => array('name' => 'alias', 'value' => preg_replace("/ +/s", "\r\n", $parser->getValue('alias'))));

$options = array();
foreach ($iplist->elem as $elem) {
    $options[] = array($elem->name, $elem->name, $parser->getValue('ip') == $elem->name);
}
$elements[] = array('type' => 'select', 'title' => __('IP'), 'br' => 1, 'info' => array('name' => 'ip', 'options' => $options));

$elements[] = array('type' => 'input_text', 'title' => __('E-mail администратора'), 'br' => 1, 'info' => array('name' => 'admin', 'value' => $parser->getValue('admin')));
$elements[] = array('type' => 'input_text', 'title' => __('Кодировка'), 'br' => 1, 'info' => array('name' => 'charset', 'value' => $parser->getValue('charset')));
$elements[] = array('type' => 'input_text', 'title' => __('Индексная страница'), 'br' => 1, 'info' => array('name' => 'index', 'value' => $parser->getValue('index')));

$options = array();
$options[] = array('asdnone', 'Отключены', $parser->getValue('autosubdomain') == 'asdnone');
$options[] = array('asddir', 'В отдельной директории', $parser->getValue('autosubdomain') == 'asddir');
$options[] = array('asdsubdir', 'В поддиректории WWW - домена', $parser->getValue('autosubdomain') == 'asdsubdir');
$elements[] = array('type' => 'select', 'title' => __('Автоподдомены'), 'br' => 1, 'info' => array('name' => 'autosubdomain', 'options' => $options));



if ($parser->hasSelect('php')) {
    $options = array();
    if ($parser->hasSelectOption('php', 'phpnone'))
        $options[] = array('phpnone', __('Отключен'), $parser->getValue('php') == 'phpnone');
    if ($parser->hasSelectOption('php', 'phpmod'))
        $options[] = array('phpmod', __('PHP как модуль Apache'), $parser->getValue('php') == 'phpmod');
    if ($parser->hasSelectOption('php', 'phpcgi'))
        $options[] = array('phpcgi', __('PHP как CGI'), $parser->getValue('php') == 'phpcgi');
    if ($parser->hasSelectOption('php', 'phpfcgi'))
        $options[] = array('phpfcgi', __('PHP как FastCGI'), $parser->getValue('php') == 'phpfcgi');
    $elements[] = array('type' => 'select', 'title' => __('PHP'), 'br' => 1, 'info' => array('name' => 'php', 'options' => $options));
}


$elements[] = array('type' => 'submit', 'br' => 0, 'info' => array('name' => 'edit', 'value' => __('Применить'))); // кнопка
$form->assign('el', $elements);
$form->display('input.form.tpl');
?>
