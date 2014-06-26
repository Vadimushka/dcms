<?php

include_once '../sys/inc/start.php';
$doc = new document();
$doc->title = __('Пользователи');

switch (@$_GET['order']) {
    case 'donate_rub':
        $order = 'donate_rub';
        $sort = 'DESC';
        $where = "WHERE `donate_rub` > '0'";
        $doc->title = __('Пожертвования');
        break;
    case 'group':
        $order = 'group';
        $sort = 'DESC';
        $where = "WHERE `group` >= '2'";
        $doc->title = __('Администрация');
        break;
    case 'login':
        $order = 'login';
        $sort = 'ASC';
        $where = '';
        break;
    case 'balls':
        $order = 'balls';
        $sort = 'DESC';
        $where = '';
        break;
    case 'rating':
        $order = 'rating';
        $sort = 'DESC';
        $where = '';
        break;
    default:
        $order = 'id';
        $sort = 'DESC';
        $where = '';
        break;
}

if (!empty($_GET['search']))
    $search = text::input_text($_GET['search']);
if (isset($search) && !$search)
    $doc->err(__('Пустой запрос'));
elseif (isset($search) && $search) {
    $where = "WHERE `login` LIKE " . $db->quote('%' . $search . '%');
    $doc->title = __('Поиск по запросу "%s"', $search);
}

$posts = array();
$pages = new pages;

$res = $db->query("SELECT COUNT(*) FROM `users` $where");
$pages->posts = $res->fetchColumn();
// меню сортировки
$ord = array();
$ord[] = array("?order=id&amp;page={$pages->this_page}" . (isset($search) ? '&amp;search=' . urlencode($search) : ''), __('ID пользователя'), $order == 'id');
$ord[] = array("?order=login&amp;page={$pages->this_page}" . (isset($search) ? '&amp;search=' . urlencode($search) : ''), __('Логин'), $order == 'login');
$ord[] = array("?order=rating&amp;page={$pages->this_page}" . (isset($search) ? '&amp;search=' . urlencode($search) : ''), __('Рейтинг'), $order == 'rating');
$ord[] = array("?order=balls&amp;page={$pages->this_page}" . (isset($search) ? '&amp;search=' . urlencode($search) : ''), __('Баллы'), $order == 'balls');
$ord[] = array("?order=group&amp;page={$pages->this_page}" . (isset($search) ? '&amp;search=' . urlencode($search) : ''), __('Статус'), $order == 'group');
$ord[] = array("?order=donate_rub&amp;page={$pages->this_page}" . (isset($search) ? '&amp;search=' . urlencode($search) : ''), __('Пожертвования'), $order == 'donate_rub');
$or = new design();
$or->assign('order', $ord);
$or->display('design.order.tpl');

$q = $db->query("SELECT `id` FROM `users` $where ORDER BY `$order` " . $sort . " LIMIT $pages->limit");

$listing = new listing();
if ($arr = $q->fetchAll()) {
    foreach ($arr AS $ank) {
        $post = $listing->post();
        $p_user = new user($ank['id']);

        $post->icon($p_user->icon());
        $post->title = $p_user->nick();
        $post->url = '/profile.view.php?id=' . $p_user->id;

        switch ($order) {
            case 'id':
                $post->content[] = '[b]' . 'ID: ' . $p_user->id . '[/b]';
                break;
            case 'group':
                $post->content[] = '[b]' . $p_user->group_name . '[/b]';
                break;
            case 'rating':
                $post->content[] = '[b]' . __('Рейтинг') . ': ' . $p_user->rating . '[/b]';
                break;
            case 'balls':
                $post->content[] = '[b]' . __('Баллы') . ': ' . ((int)$p_user->balls) . '[/b]';
                break;
            case 'donate_rub':
                $post->content[] = '[b]' . __('Сумма пожертвований: %s руб.', $p_user->donate_rub) . '[/b]';
                break;
        }

        $post->content[] = '[small]' . __('Дата регистрации') . ': ' . date('d-m-Y', $p_user->reg_date) . '[/small]';
        $post->content[] = '[small]' . __('Последний визит') . ': ' . misc::when($p_user->last_visit) . '[/small]';
    }
}

$form = new form('?', false);
$form->hidden('order', $order);
$form->text('search', __('Ник или его часть'), @$search, false);
$form->button(__('Поиск'));
$form->display();

$listing->display($order == 'donate_rub' ? __('Нет пользователей, пожертвовавших денег на проект') : __('Нет пользователей'));

$pages->display("?order=$order&amp;" . (isset($search) ? 'search=' . urlencode($search) . '&amp;' : ''));