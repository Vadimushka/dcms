<?php
include_once '../sys/inc/start.php';
$doc = new document(5);
$doc->title = __('Добавить страницу');

if( isset($_POST['name']) && isset($_POST['description']) && isset($_GET['bd_complete']) ){
    $name = text::for_name($_POST['name']);
    $description = text::input_text($_POST['description']);
	
    if(!$name){
        $doc->err(__('Введите название страницы.'));
    }elseif(!$description){
        $doc->err(__('Введите содержание страницы.'));
    }else{
        mysql_query("INSERT INTO `pages` (`name`, `description`) VALUES ('". my_esc($name) ."', '". my_esc($description) ."')");
		$id_category = mysql_insert_id();
        $dcms->log('Страницы', 'Создание страницы "' . $name . '"');
        $doc->msg(__('Страница успешно создана'));
		$doc->ret(__('Просмотреть страницу'), 'index.php?id=' . $id_category);
		exit;
    }
}

if($_GET['post'] == 'base'){
    $form = new form('?bd_complete');
    $form->text('name', __('Название страницы:'));
    $form->textarea('description', __('Содержимое страницы:'));
    $form->button(__('Отправить данные на сервер'));
    $form->display();
}elseif($_GET['post'] == 'file'){
    if(isset($_POST['download']))
    {
        $handle = new upload($_FILES['userfile']);
        
        if($handle->uploaded)
        {
            $handle->process($_SERVER['DOCUMENT_ROOT'].'/pages/files/');
            if($handle->processed)
            {
                $handle->clean();
            }else{
                $doc->err($handle->error);
            }
        }
    }
    
    $form = new form('?post=file');
    $form->file('userfile',__('Выбери .txt файл'), false);
    $form->button(__('Загрузить'), 'download');
    $form->display(); 
}elseif($_GET['post'] == 'html'){
	if(isset($_POST['addhtml']))
	{
		$file_name = text::translit($_POST['name']).'.html';
		$file_name_ras = text::translit($_POST['name']).'.ini';
		if(!file_exists($file_name) && !file_exists($file_name_ras))
		{
			$ras = fopen('html/'.$file_name_ras, 'w+');
			fclose($ras);
			
			
			$fp = fopen('html/'.$file_name, 'w+');
			$str = 'Этот файл был создан с помощь StaticPages for DCMS. Теперь его надо отредкатировать.';
			$write = fwrite($fp, $str);
			if($write){ $doc->msg('Страница успешно создана. Приступите к редактированию.');
				$doc->ret(__('Отредактировать'), 'edit.html.php?name='.$file_name);}
			else $doc->err('Ошибка записи.');
			fclose($fp);
		}
	}

	$form = new form('?post=html');
	$form->text('name', __('Имя страницы'));
	$form->button(__('Создать'), 'addhtml');
	$form->bbcode('* Имя страницы вводится на транслите');
	$form->display();
}else{
    if(isset($_GET['fileorbd']) && isset($_POST['file']))
        header('Location: add.pages.php?post=file');
    if(isset($_GET['fileorbd']) && isset($_POST['get_bd']))
        header('Location: add.pages.php?post=base');
	if(isset($_GET['fileorbd']) && isset($_POST['html']))
		header('Location: add.pages.php?post=html');
    $form = new form('?fileorbd');
    $form->bbcode(__('Выбери какой использовать метод для создания статистической страницы.'));
    $form->button(__('Загрузить файл'), 'file', false);
    $form->button(__('Загрузить содержимое в Базу Данных'), 'get_bd');
	$form->button(__('HTML страница'), 'html');
    $form->display();
}
?>