<?php

include_once '../sys/inc/start.php';
$doc = new document();


// результаты
$searched = &$_SESSION['search']['result'];
// маркеры (выделение найденых слов)
$searched_mark = &$_SESSION['search']['mark'];
// запрос
$search_query = &$_SESSION['search']['query'];
// запрос (массив для mysql)
$search_query_sql = &$_SESSION['search']['query_sql'];
$doc->title = __('Поиск');

if ($dcms->forum_search_reg && !$user->group) {
    $doc->err(__('Поиск по форуму доступен только зарегистрированым пользователям'));
    $doc->ret(__('К категориям'), './');
    exit;
}



if (!isset($_GET['cache']) || empty($searched)) {
    $searched = array();
    $search_query = null;
    $search_query_sql = array();
    $searched_mark = array();
}
if (!empty($_POST['query'])) {
    if ($dcms->forum_search_captcha && (empty($_POST['captcha']) || empty($_POST['captcha_session']) || !captcha::check($_POST['captcha'], $_POST['captcha_session'])))
        $doc->err(__('Код с картинки введен неверно'));
    else {
        $stemmer = new stemmer();
        $searched = array();
        $search_query = text::input_text($_POST['query']);
        $search_query_sql = array();
        $searched_mark = array();
        // массив всех слов
        $search_array = preg_split('#\s+#u', text::input_text($_POST['query']));
        // текст запроса без лишних пробелов
        $search_query = implode(' ', $search_array);
        for ($i = 0; $i < count($search_array); $i++) {
            $st = $stemmer->stem_word($search_array[$i]);
            // пропускаем слова, состоящие менее чем из 3-х символов
            if (text::strlen($st) < 3)
                continue;
            // составляем регулярки для подсведки найденных слов
            $searched_mark[$i] = '#([^\[].*)(' . preg_quote($st, '#') . '[a-zа-я0-9]*)([^\]].*)#ui';
            $search_query_sql[$i] = '+' . my_esc($st) . '*';
        }
        $q = mysql_query("SELECT `forum_themes`.`id`,`forum_themes`.`name`, `forum_messages`.`message`, `forum_messages`.`id` AS `id_message` FROM `forum_themes`
LEFT JOIN `forum_messages` ON `forum_themes`.`id` = `forum_messages`.`id_theme`
WHERE `forum_themes`.`group_show` <= '$user->group' AND  (`forum_messages`.`group_show` IS NULL OR `forum_messages`.`group_show` <= '$user->group')
AND MATCH (`forum_themes`.`name`,`forum_messages`.`message`) AGAINST ('" . implode(' ', $search_query_sql) . "' IN BOOLEAN MODE)
GROUP BY `forum_themes`.`id`");
        while ($result = mysql_fetch_assoc($q)) {
            $searched[] = $result;
        }
    }
}



$listing = new listing();

$posts = array();
$pages = new pages;
$pages->posts = count($searched);
$pages->this_page(); // получаем текущую страницу
// конец цикла
$end = min($pages->items_per_page * $pages->this_page, $pages->posts);
$start = $pages->my_start();
for ($i = $start; $i < $end; $i++) {
    $post = $listing->post();


    $theme = $searched[$i];
    $title = preg_replace($searched_mark, '\1<span class="mark">\2</span>\3', for_value($theme['name']));
    $post->content = output_text(preg_replace($searched_mark, '\1[mark]\2[/mark]\3', $theme['message']));

    $post->title = $title;
    $post->url = 'theme.php?id=' . $theme['id'];


    if ($post->content) {
        $post->content .= "<br /><a href='message.php?id_message=$theme[id_message]&amp;return=" . urlencode('search.php?cache&page=' . $pages->this_page) . "'>К сообщению</a>";
    }
}


$listing->display($search_query ? __('Результаты по запросу "%s" отсутствуют', $search_query) : false);


$pages->display('?cache&amp;'); // вывод страниц

$smarty = new design();
$smarty->assign('method', 'post');
$smarty->assign('action', '?' . passgen());
$elements = array();
$elements[] = array('type' => 'input_text', 'title' => __('Что ищем'), 'br' => 0, 'info' => array('name' => 'query', 'value' => $search_query));

if ($dcms->forum_search_captcha) {
    $elements[] = array('type' => 'captcha', 'session' => captcha::gen(), 'br' => 1);
}

$elements[] = array('type' => 'submit', 'br' => 0, 'info' => array('value' => __('Поиск'))); // кнопка
$smarty->assign('el', $elements);
$smarty->display('input.form.tpl');
$doc->ret(__('Форум'), './');
?>