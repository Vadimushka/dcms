<?php

include_once '../sys/inc/start.php';
$doc = new document(2);
$doc->title = __('Вход в админку');

if (!dpanel::is_access() && (empty($_POST['captcha_session']) || empty($_POST['captcha']))) {
    $doc->msg(__('Для входа в админку необходимо пройти капчу'));
} elseif (!dpanel::is_access() && !captcha::check($_POST['captcha'], $_POST['captcha_session'])) {
    $doc->err(__('Вы ошиблись при вводе чисел с картинки'));
} else {
    dpanel::access(); // разрешаем доступ к админке

    $doc->msg(__('Отлично, переходим в админку'));

    if (!empty($_GET['return'])) {
        header('Refresh: 1; url=' . $_GET['return']);
        $doc->ret(__('Вернуться'), for_value($_GET['return']));
    } else {
        header('Refresh: 1; url=./?' . SID);
        $doc->ret(__('В админку'), '/dpanel/');
    }

    exit;
}

$form = new design();
$form->assign('method', 'post');
$form->assign('action', '?' . passgen() . (isset($_GET['return']) ? '&amp;return=' . urlencode($_GET['return']) : null));
$elements = array();
$elements[] = array('type' => 'captcha', 'session' => captcha::gen(), 'br' => 1);

if (preg_match('#Opera#ui', $dcms->browser)) {
    $elements [] = array('type' => 'text', 'value' => output_text(__('Функция Turbo должна быть отключена')), 'br' => 1);
}

$elements[] = array('type' => 'submit', 'br' => 0, 'info' => array('value' => __('Войти'))); // кнопка
$form->assign('el', $elements);
$form->display('input.form.tpl');
?>
