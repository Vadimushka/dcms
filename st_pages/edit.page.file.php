<?php
include_once 'inc/fnc.php';
include_once '../sys/inc/start.php';
$doc = new document(5);
$doc -> title = __('Редактирование страницы');

$files = array();
$files_g = (array) glob(H. '/st_pages/files/*.txt');
foreach($files_g as $path){
    if (preg_match("#([^/]*?)\.txt#", $path, $m)) {
        $files[] = $m[1];
    }
}

$files = array_reverse($files);

if(isset($_GET['complete']) && $_POST['edit'])
{
    $path = 'files/'.$_GET['name'].'.txt';
    $file = fopen($path, w);
    fputs($file, $_POST['textpage']);
    fclose($file);
    $doc->msg('Успешно');
    $doc->act(__('Просмотреть страницу'), 'files.php?name='.$_GET['name']);
}else{
    if(!empty($_GET['name']))
    {
        if (!in_array($_GET['name'], $files)) {
            $doc->err(__('Страница не найдена, возможно она была удалена с сервера'));
        }else{
            $fname = 'files/'.$_GET['name'].'.txt';
            $file_handle = fopen($fname, 'r');
            $form = new form('?complete&amp;name='.$_GET['name']);
            $count_file = count_line_files($_GET['name']);
            $line = fread($file_handle, $count_file);
            $form->textarea('textpage', 'Текст страницы', $line);
            $form->button('Отправить данные', 'edit');
            $form->display();
        }
    }
}
?>