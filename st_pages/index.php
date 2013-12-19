<?php

include_once '../sys/inc/start.php';
$doc = new document();
$doc->title = __('Страница');
?>
<script type="text/javascript" src="//vk.com/js/api/openapi.js?98"></script>
<script type="text/javascript" src="http://vk.com/js/api/share.js?90" charset="windows-1251"></script>

<script type="text/javascript">
  VK.init({apiId: <?php echo $dcms->apiid; ?>, onlyWidgets: true}); <!-- сюда вводим Ваш ApiID -->
</script>

<?php
$id = $_GET['id'];

if($id == NULL){
	$doc->err('Идентификатор "id" не задан.');
	if ($user->group >= 5) 
		$doc->act(__('Создать страницу'), 'add.pages.php');
        header("Refresh: 1; url=./spisok.str.php");
	exit;
}

$q = mysql_query("SElECT * FROM `pages`");

if(!mysql_num_rows($q)){
	$doc->err('Идентификатор "id" введён не верно, или страница для вас недоступна.');
	if ($user->group >= 5) 
		$doc->act(__('Создать страницу'), 'add.pages.php');
	exit;
}

$q1 = mysql_query("SELECT * FROM `pages` WHERE `id` = '$id'");
$title = mysql_fetch_assoc($q1);
$doc->title .= ' - '.$title['name'];

$q2 = mysql_query("SELECT * FROM `pages` WHERE `id` = '$id'");
while($pages = mysql_fetch_assoc($q2))
{
    $listing = new listing();
    $post = $listing->post();
    $post->title = '<h1><center>'.$pages['name'].'</center></h1><br/>';
    $post->content = text::for_opis($pages['description']);
    $post->display();
    if($pages['comments_vk'] == 1)
    {
        $post = $listing->post();
		$post->content = '<script type="text/javascript"><!--
document.write(VK.Share.button(false,{type: "link", text: "Сохранить VK", eng: 1}));
--></script> <div id="vk_like"></div>
<script type="text/javascript">
VK.Widgets.Like("vk_like", {type: "full", height: 18});
</script>';
		$post->display();
		
        $post->content = '<div id="vk_comments"></div>
<script type="text/javascript">
VK.Widgets.Comments("vk_comments", {limit: 10, width: "$dcms->vkwidth", attach: "*"});
</script>';
        $post->display();
    }
}

if ($user->group >= 5) {
$doc->act(__('Создать страницу'), 'add.pages.php');
$doc->act(__('Редактировать страницу'), 'edit.page.php?id='.$id);
$doc->act(__('Удалить страницу'), 'del.page.php?id='.$id);
}