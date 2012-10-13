<?php
include_once '../sys/inc/start.php';
$doc = new document();
$doc->title = 'Добавить домен (DNS)';
$isp = new ispmanager();
include_once 'inc/check_login.php';
$doc->ret('Панель управления', 'index.php');
$doc->ret('Список доменов', 'domain.php');
$iplist = $isp->getData('iplist');

$parser = new html_parser($isp->getData('domain.edit', array('out' => 'html', 'plid' => '')));



if (isset($_POST['create'])) {
    $data = array();
    $data['name'] = @$_POST['name'];
    
    $data['ip'] = @$_POST['ip'];
    $data['mx'] = preg_replace("/[\n\r\t]+/s", ' ', @$_POST['mx']);
    $data['ns'] = preg_replace("/[\n\r\t]+/s", ' ', @$_POST['ns']);    
    
    $data['webdomain'] = @$_POST['webdomain'];    
    $data['maildomain'] = @$_POST['maildomain'];
    
    $data['elid'] = '';
    $data['sok'] = 'ok';

    $result = $isp->getData('domain.edit', $data);
   // echo '<pre>' . print_r($result, true) . '</pre>';
    if ($result->ok) {
        $doc->msg('Домен успешно добавлен');
        $listing = new listing();
        $post = $listing->post();
        $post->icon('domain');
        $post->url = 'domain.php';
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
$elements[] = array('type' => 'input_text', 'title' => __('Имя домена'), 'br' => 1, 'info' => array('name' => 'name'));

$options = array();
foreach ($iplist->elem as $elem) {
    $options[] = array($elem->name, $elem->name);
}
$elements[] = array('type' => 'select', 'title' => __('IP'), 'br' => 1, 'info' => array('name' => 'ip', 'options' => $options));


$elements[] = array('type' => 'textarea', 'title' => __('Серверы имен'), 'br' => 1, 'info' => array('name' => 'ns', 'value' => preg_replace("/ +/s", "\r\n", $parser->getValue('ns'))));
$elements[] = array('type' => 'textarea', 'title' => __('Почтовые серверы'), 'br' => 1, 'info' => array('name' => 'mx', 'value' => preg_replace("/ +/s", "\r\n", $parser->getValue('mx'))));

$elements [] = array('type' => 'checkbox', 'br' => 1, 'info' => array('value' => 'on', 'name' => 'webdomain', 'text' => __('Создать WWW домен')));
$elements [] = array('type' => 'checkbox', 'br' => 1, 'info' => array('value' => 'on', 'name' => 'maildomain', 'text' => __('Создать почтовый домен')));

$elements[] = array('type' => 'submit', 'br' => 0, 'info' => array('name' => 'create', 'value' => __('Создать'))); // кнопка
$form->assign('el', $elements);
$form->display('input.form.tpl');

?>
