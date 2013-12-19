<?php
include_once '../sys/inc/start.php';
$doc = new document(5);
$doc->title = __('Редактировать страницу');
$groups = groups::load_ini();

$id = $_GET['id'];

$q = mysql_query("SELECT * FROM `pages` WHERE `id` = '$id'");

$pages = mysql_fetch_assoc($q);

$doc->title .= ' - ' .$pages['name'];

if(isset($_POST['save'])){
	if (isset($_POST['name']) && isset($_POST['description'])) {
		$name = text::for_name($_POST['name']);
		$description = text::input_text($_POST['description']);
        $group_show = (int) $_POST['group_show'];
        
        if(isset($_POST['comments_vk']))
            mysql_query("UPDATE `pages` SET `name` = '". my_esc($name) ."', `description` = '". my_esc($description) ."', `comments_vk` = '1' WHERE `id` = '$id' LIMIT 1");
        else
            mysql_query("UPDATE `pages` SET `name` = '". my_esc($name) ."', `description` = '". my_esc($description) ."', `comments_vk` = '0' WHERE `id` = '$id' LIMIT 1");
		
		$id_category = mysql_insert_id();
		$dcms->log('Страницы', 'Редактирование страницы "' . $name . '"'); 
		$doc->msg(__('Страница успешно отредактирована'));
		$doc->ret(__('Просмотреть страницу'), 'index.php?id=' . $id);
		exit;
	}
}

$form = new form('?id='.$id);
$form->text('name', __('Название страницы'), $pages['name']);
$form->textarea('description', __('Содержимое страницы'), $pages['description']);
if($pages['comments_vk'] == 0)
    $form->checkbox('comments_vk', __('Сервисы ВК'), false);
if($pages['comments_vk'] == 1)
    $form->checkbox ('comments_vk', __('Сервисы ВК'), true);
$form->button(__('Отправить данные на сервер'), 'save');
$form->display();

$doc->ret(__('Назад'), 'index.php?id=' . $id);
?>