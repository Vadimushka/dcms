<?php
include_once '../sys/inc/start.php';
$doc = new document(5);
$doc->title = __('Список страниц');
if($_GET['sort'] == NULL)
{
echo '<center>
<div class="body_blok_sort">
<span class="razdel">Сортировка:</span>
 <span class="link"><a href = "/st_pages/spisok.str.php?sort=files">По файлам на сервере</a> | По данным в БД
 | <a href="?sort=html">HTML</a></span>
 </div>
 </center>';
 }elseif($_GET['sort'] == 'files'){
echo '<center>
<div class="body_blok_sort">
<span class="razdel">Сортировка:</span>
 <span class="link">По файлам на сервере | <a href="/st_pages/spisok.str.php">По данным в БД</a> | <a href="?sort=html">HTML</a>
 </span>
 </div>
 </center>';    
 }else{
 echo '<center><div class="body_blok_sort">
<span class="razdel">Сортировка:</span>
 <span class="link"><a href = "/st_pages/spisok.str.php?sort=files">По файлам на сервере</a> | <a href="/st_pages/spisok.str.php">По данным в БД</a>| HTML
 </span>
 </div>
 </center>'; 
 }
if($_GET['sort'] == 'html'){
    $files = array();
    $files_g = (array) glob(H . '/st_pages/html/*.html');
    foreach ($files_g as $path) {
        if (preg_match("#([^/]*?)\.html#", $path, $m)) {
            $files[] = $m[1];
        }
    }

    $files = array_reverse($files);
    
    $listing = new listing();
    foreach ($files AS $name) {
        $post = $listing->post();
        $post->title = for_value($name);
        $post->url = '/st_pages/html.php?name=' . urlencode($name);
        $post->action('edit', 'edit.html.php?name='.$name);
        $post->icon('doc');    
    }
    $listing ->display(__('Страницы отсутствуют'));
    $doc->act('Добавить страницу', 'add.pages.php?post=file');
}
elseif($_GET['sort'] == 'files')
{
    $files = array();
    $files_g = (array) glob(H . '/st_pages/files/*.txt');
    foreach ($files_g as $path) {
        if (preg_match("#([^/]*?)\.txt#", $path, $m)) {
            $files[] = $m[1];
        }
    }

    $files = array_reverse($files);
    
    $listing = new listing();
    foreach ($files AS $name) {
        $post = $listing->post();
        $post->title = for_value($name);
        $post->url = '/st_pages/files.php?name=' . urlencode($name);
        $post->action('edit', 'edit.page.file.php?name='.$name);
        $post->icon('doc');    
    }
    $listing ->display(__('Страницы отсутствуют'));
    $doc->act('Добавить страницу', 'add.pages.php?post=file');
}else{
    $query = mysql_query("SELECT * FROM `pages`");
    
    $listing = new listing();
    while($str = mysql_fetch_assoc($query))
    {
        $post = $listing->post();
        $post->title = $str['name'];
        $post->url = '/st_pages/index.php?id='.$str['id'];
        $post->action('edit', 'edit.page.php?id='.$str['id']);
        $post->icon('doc');
    }
    $listing->display('Страницы отсутствуют');
    $doc->ret(__('Админка'), '/dpanel/pages.php');
    $doc->act('Добавить страницу', 'add.pages.php?post=base');
}
?>