<?php

include_once '../sys/inc/start.php';
$groups = groups::load_ini(); // загружаем массив групп
$doc = new document();
$doc->title = __('Редактирование категории');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Refresh: 1; url=./');
    $doc->err(__('Ошибка выбора категории'));
    exit;
}
$id_category = (int) $_GET['id'];

$q = mysql_query("SELECT * FROM `forum_categories` WHERE `id` = '$id_category' AND `group_edit` <= '$user->group'");

if (!mysql_num_rows($q)) {
    header('Refresh: 1; url=./');
    $doc->err(__('Категория не доступна для редактирования'));
    exit;
}

$category = mysql_fetch_assoc($q);

if (isset($_POST['save'])) {
    if (isset($_POST['name']) && isset($_POST['description'])) {
        $name = text::for_name($_POST['name']);
        $description = text::input_text($_POST['description']);

        if ($name && $name != $category['name']) {
            $dcms->log('Форум', 'Изменение названия категории "' . $category['name'] . '" на [url=/forum/category.php?id=' . $category['id'] . ']"' . $name . '"[/url]');
            $category['name'] = $name;
            mysql_query("UPDATE `forum_categories` SET `name` = '" . my_esc($category['name']) . "' WHERE `id` = '$category[id]' LIMIT 1");
            $doc->msg(__('Название категории успешно изменено'));
        }

        if ($description != $category['description']) {
            $category['description'] = $description;
            mysql_query("UPDATE `forum_categories` SET `description` = '" . my_esc($category['description']) . "' WHERE `id` = '$category[id]' LIMIT 1");
            $doc->msg(__('Описание категории успешно изменено'));
            $dcms->log('Форум', 'Изменение описания категории [url=/forum/category.php?id=' . $category['id'] . ']"' . $category['name'] . '"[/url]');
        }
    }

    if (isset($_POST['position'])) { // позиция
        $position = (int) $_POST['position'];
        if ($position != $category['position']) {
            $dcms->log('Форум', 'Изменение позиции категории [url=/forum/category.php?id=' . $category['id'] . ']"' . $category['name'] . '"[/url] с ' . $category['position'] . ' на ' . $position);

            $category['position'] = $position;
            mysql_query("UPDATE `forum_categories` SET `position` = '$category[position]' WHERE `id` = '$category[id]' LIMIT 1");
            $doc->msg(__('Позиция категории успешно изменена'));
            $dcms->log('Форум', 'Изменение позиции категории [url=/forum/category.php?id=' . $category['id'] . ']"' . $category['name'] . '"[/url] на ' . $position);
        }
    }

    if (isset($_POST['group_show'])) { // просмотр
        $group_show = (int) $_POST['group_show'];
        if (isset($groups[$group_show]) && $group_show != $category['group_show']) {
            $category['group_show'] = $group_show;
            mysql_query("UPDATE `forum_categories` SET `group_show` = '$category[group_show]' WHERE `id` = '$category[id]' LIMIT 1");
            $doc->msg(__('Просматривать категорию теперь разрешено группе "%s" и выше', groups::name($group_show)));
            $dcms->log('Форум', 'Изменение прав на просмотр категории [url=/forum/category.php?id=' . $category['id'] . ']"' . $category['name'] . '"[/url] для группы ' . groups::name($group_show));
        }
    }

    if (isset($_POST['group_write'])) { // запись
        $group_write = (int) $_POST['group_write'];
        if (isset($groups[$group_write]) && $group_write != $category['group_write']) {
            if ($category['group_show'] > $group_write)
                $design->err(__('Для того, чтобы создавать разделы группе "%s" сначала необходимо дать права на просмотр категории', groups::name($group_write)));
            else {
                $category['group_write'] = $group_write;
                mysql_query("UPDATE `forum_categories` SET `group_write` = '$category[group_write]' WHERE `id` = '$category[id]' LIMIT 1");
                $design->msg(__('Создавать разделы теперь разрешено группе "%s" и выше', groups::name($group_write)));
                $dcms->log('Форум', 'Изменение прав на создание разделов в категории [url=/forum/category.php?id=' . $category['id'] . ']"' . $category['name'] . '"[/url] для группы ' . groups::name($group_write));
            }
        }
    }

    if (isset($_POST['group_edit'])) { // редактирование
        $group_edit = (int) $_POST['group_edit'];
        if (isset($groups[$group_edit]) && $group_edit != $category['group_edit']) {
            if ($category['group_write'] > $group_edit)
                $design->err(__('Для изменения параметров категории группе "%s" сначала необходимо дать права на создание разделов', groups::name($group_edit)));
            else {
                $category['group_edit'] = $group_edit;
                mysql_query("UPDATE `forum_categories` SET `group_edit` = '$category[group_edit]' WHERE `id` = '$category[id]' LIMIT 1");
                $design->msg(__('Изменять параметры категории теперь разрешено группе "%s" и выше', groups::name($group_edit)));
                $dcms->log('Форум', 'Изменение прав на изменение параметров категории [url=/forum/category.php?id=' . $category['id'] . ']"' . $category['name'] . '"[/url] для группы ' . groups::name($group_write));
            }
        }
    }
}

$doc->title = __('Редактирование категории "%s"', $category['name']); // шапка страницы

$smarty = new design();
$smarty->assign('method', 'post');
$smarty->assign('action', "?id=$category[id]&amp;" . passgen() . (isset($_GET['return']) ? '&amp;return=' . urlencode($_GET['return']) : null));
$elements = array();
$elements[] = array('type' => 'input_text', 'title' => __('Название'), 'br' => 1, 'info' => array('name' => 'name', 'value' => $category['name']));
$elements[] = array('type' => 'textarea', 'title' => __('Описание'), 'br' => 1, 'info' => array('name' => 'description', 'value' => $category['description']));

$elements[] = array('type' => 'input_text', 'title' => __('Позиция'), 'br' => 1, 'info' => array('name' => 'position', 'value' => $category['position']));

$options = array();
foreach ($groups as $type => $value) {
    $options[] = array($type, $value['name'], $type == $category['group_show']);
}
$elements[] = array('type' => 'select', 'br' => 1, 'title' => __('Просмотр разделов'), 'info' => array('name' => 'group_show', 'options' => $options));

$options = array();
foreach ($groups as $type => $value) {
    $options[] = array($type, $value['name'], $type == $category['group_write']);
}
$elements[] = array('type' => 'select', 'br' => 1, 'title' => __('Создание разделов'), 'info' => array('name' => 'group_write', 'options' => $options));

$options = array();
foreach ($groups as $type => $value) {
    $options[] = array($type, $value['name'], $type == $category['group_edit']);
}
$elements[] = array('type' => 'select', 'br' => 1, 'title' => __('Изменение параметров'), 'info' => array('name' => 'group_edit', 'options' => $options));

$elements[] = array('type' => 'text', 'value' => '* '.__('Будьте внимательнее при установке доступа выше своего.'), 'br' => 1);
// $elements[]=array('type'=>'textarea', 'title'=>'Редактирование сообщения', 'br'=>1, 'info'=>array('name'=>'message','value'=>$message['message']));
$elements[] = array('type' => 'submit', 'br' => 0, 'info' => array('name' => 'save', 'value' => __('Применить изменения'))); // кнопка
$smarty->assign('el', $elements);
$smarty->display('input.form.tpl');

$doc->act(__('Чистить категорию'), 'category.clear.php?id=' . $category['id']);
$doc->act(__('Удалить категорию'), 'category.delete.php?id=' . $category['id']);

if (isset($_GET['return']))
    $doc->ret(__('В категорию'), for_value($_GET['return']));
else
    $doc->ret(__('В категорию'), 'category.php?id=' . $category['id']);

$doc->ret(__('Форум'), './');
?>