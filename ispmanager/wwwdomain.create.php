<?php

include_once '../sys/inc/start.php';
$doc = new document();
$doc->title = 'Создание WWW - домена';
$isp = new ispmanager();
include_once 'inc/check_login.php';
$doc->ret('Панель управления', 'index.php');
$doc->ret('Список доменов', 'wwwdomain.php');
$iplist = $isp->getData('iplist');

$parser = new html_parser($isp->getData('wwwdomain.edit', array('out' => 'html')));

if (isset($_POST['create'])) {
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
    $data['elid'] = '';
    $data['sok'] = 'ok';

    $result = $isp->getData('wwwdomain.edit', $data);

    if ($result->ok) {
        $doc->msg('Домен успешно добавлен');
        $listing = new listing();
        $post = $listing->post();
        $post->icon('www');
        $post->url = 'wwwdomain.php';
        $post->title = 'Список доменов';
        $post = $listing->post();
        $post->icon('create');
        $post->url = '?';
        $post->title = 'Создать еще один домен';
        $listing->display();
        exit;
    } else {
        if ($result->error) {
            $doc->err(__('Поле %s не заполнено', $result->error['val']));
        } else {
            $doc->err('Не удалось создать домен');
        }
    }
}

$form = new design();
$form->assign('method', 'post');
$form->assign('action', '?' . passgen());
$elements = array();
$elements[] = array('type' => 'input_text', 'title' => __('Имя домена'), 'br' => 1, 'info' => array('name' => 'domain'));
$elements[] = array('type' => 'textarea', 'title' => __('Псевдонимы'), 'br' => 1, 'info' => array('name' => 'alias'));
$elements[] = array('type' => 'input_text', 'title' => __('Корневая папка'), 'br' => 1, 'info' => array('name' => 'docroot', 'value' => 'auto'));

$options = array();
foreach ($iplist->elem as $elem) {
    $options[] = array($elem->name, $elem->name);
}
$elements[] = array('type' => 'select', 'title' => __('IP'), 'br' => 1, 'info' => array('name' => 'ip', 'options' => $options));

$elements[] = array('type' => 'input_text', 'title' => __('E-mail администратора'), 'br' => 1, 'info' => array('name' => 'admin', 'value' => 'webmaster@'));
$elements[] = array('type' => 'input_text', 'title' => __('Кодировка'), 'br' => 1, 'info' => array('name' => 'charset', 'value' => 'UTF-8'));
$elements[] = array('type' => 'input_text', 'title' => __('Индексная страница'), 'br' => 1, 'info' => array('name' => 'index', 'value' => 'index.php'));

$options = array();
$options[] = array('asdnone', 'Отключены');
$options[] = array('asddir', 'В отдельной директории');
$options[] = array('asdsubdir', 'В поддиректории WWW - домена');
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

$elements[] = array('type' => 'submit', 'br' => 0, 'info' => array('name' => 'create', 'value' => __('Создать'))); // кнопка
$form->assign('el', $elements);
$form->display('input.form.tpl');
?>
