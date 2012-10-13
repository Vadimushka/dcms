<?php
include_once '../sys/inc/start.php';
$doc = new document(4);
$doc->title = __('Редактирование новости');
$doc->ret (__('К новостям'), './');

$id = (int)@$_GET['id'];

$q = mysql_query("SELECT * FROM `news` WHERE `id` = '$id' LIMIT 1");

if (!mysql_num_rows($q))$doc->access_denied(__('Новость не найдена или удалена'));

$news = mysql_fetch_assoc($q);

$ank = new user($news['id_user']);

if ($ank->group > $user->group)$doc->access_denied(__('У Вас нет прав для редактирования данной новости'));

$news_e = &$_SESSION['news_edit'];

if (isset($_POST['clear']))$news_e = array();

if (empty($news_e)) {
    $news_e = array();
    $news_e['title'] = $news['title'];
    $news_e['text'] = $news['text'];
    $news_e['checked'] = false;
}

if ($news_e['checked'] && isset($_POST['send'])) {
    if (empty($_POST['captcha']) || empty($_POST['captcha_session']) || !captcha::check($_POST['captcha'], $_POST['captcha_session']))
        $doc->err(__('Ошибка при вводе чисел с картинки'));
    else {
        mysql_query("UPDATE `news` SET `title` = '" . my_esc($news_e['title']) . "', `id_user` = '$user->id', `text` = '" . my_esc($news_e['text']) . "', `sended` = '0' WHERE `id` = '$id' LIMIT 1");

        $doc->msg(__('Новость успешно отредактирована'));
        $news_e = array();
        header('Refresh: 1; ./');
        exit;
    }
}

if (isset($_POST['edit']))$news['checked'] = 0;

if (isset($_POST['next'])) {
    $title = text::for_name($_POST['title']);
    $text = text::input_text($_POST['text']);

    if (!$title)$doc->err(__('Заполните "Заголовок новости"'));
    else $news_e['title'] = $title;
    if (!$text)$doc->err(__('Заполните "Текст новости"'));
    else $news_e['text'] = $text;

    if ($title && $text)$news_e['checked'] = 1;
}

$smarty = new design();
$smarty->assign('method', 'post');
$smarty->assign('action', '?id=' . $id . '&amp;' . passgen());
$elements = array();
$elements[] = array('type' => 'input_text', 'title' => __('Заголовок новости'), 'br' => 1, 'info' => array('name' => 'title', 'value' => $news_e['title'] , 'disabled' => $news_e['checked']));
$elements[] = array('type' => 'textarea', 'title' => __('Текст новости'), 'br' => 1, 'info' => array('name' => 'text', 'value' => $news_e['text'], 'disabled' => $news_e['checked']));

if ($news_e['checked']) {
    $elements[] = array('type' => 'captcha', 'session' => captcha::gen(), 'br' => 1);
    $elements[] = array('type' => 'submit', 'br' => 0, 'info' => array('name' => 'edit', 'value' => __('Редактировать'))); // кнопка
    $elements[] = array('type' => 'submit', 'br' => 0, 'info' => array('name' => 'send', 'value' => __('Применить'))); // кнопка
}else {
    $elements[] = array('type' => 'submit', 'br' => 0, 'info' => array('name' => 'clear', 'value' => __('Очистить'))); // кнопка
    $elements[] = array('type' => 'submit', 'br' => 0, 'info' => array('name' => 'next', 'value' => __('Далее'))); // кнопка
}

$smarty->assign('el', $elements);
$smarty->display('input.form.tpl');

?>
