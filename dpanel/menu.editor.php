<?php

include_once '../sys/inc/start.php';
dpanel::check_access();
$doc = new document(6);
$groups = groups::load_ini();
$doc->title = __('Редактор меню');
// поиск файлов меню, подготовка списка (массива)
$menu_files = (array) glob(H . '/sys/ini/menu.*.ini');
$menus = array();
foreach ($menu_files as $menu_path) {
    if (!preg_match('#menu\.(.+?)\.ini$#ui', $menu_path, $m))
        continue;
    $menus[] = $m[1];
}

if (!empty($_GET['menu'])) {
    $menu = (string) $_GET['menu'];

    if (!in_array($menu, $menus)) {
        $doc->err(__('Запрошенное меню не найдено'));
        exit;
    }
    $doc->title = __('Меню "%s" - редактирование', $menu);
    $m_obj = new menu($menu);

    if (!empty($_GET['item'])) {
        $item_name = (string) $_GET['item'];
        if (!isset($m_obj->menu_arr[$item_name])) {
            $doc->err(__('Ошибка при выборе пункта меню'));
        }

        $item = $m_obj->menu_arr[$item_name];
        $doc->title = __('Меню "%s" - %s', $menu, $item_name);
        if (!empty($_POST['delete'])) {
            if (empty($_POST['captcha']) || empty($_POST['captcha_session']) || !captcha::check($_POST['captcha'], $_POST['captcha_session'])) {
                $doc->err(__('Проверочное число введено неверно'));
            } else {
                $ini = ini::read(H . '/sys/ini/menu.' . $menu . '.ini', true);
                unset($ini[$item_name]);
                if (ini::save(H . '/sys/ini/menu.' . $menu . '.ini', $ini, true)) {
                    $doc->msg(__('Пункт меню успешно удален'));
                } else {
                    $doc->err(__('Ошибка при сохранении файла'));
                }
                header('Refresh: 1; url=?menu=' . urlencode($menu) . '&amp;' . passgen());
                $doc->ret(__('Меню "%s"', $menu), '?menu=' . urlencode($menu) . '&amp;' . passgen());
                $doc->ret(__('Список меню'), '?' . passgen());
                $doc->ret(__('Админка'), './?' . passgen());
                exit;
            }
        }
        if (!empty($_POST['save'])) {
            $name = text::input_text(@$_POST['name']);
            $url = text::input_text(@$_POST['url']);
            $icon = text::input_text(@$_POST['icon']);
            $razdel = (int) !empty($_POST['razdel']);
            $is_vip = (int) !empty($_POST['is_vip']);
            $group = (int) @$_POST['group'];
            $position = (int) @$_POST['position'];
            if (empty($name)) {
                $doc->err(__('Название не может быть пустым'));
            } elseif ($name != $item_name && isset($m_obj->menu_arr[$name])) {
                $doc->err(__('Выбранное название меню уже занято'));
            } else {
                $ini = ini::read(H . '/sys/ini/menu.' . $menu . '.ini', true);
                if ($name != $item_name) {
                    unset($ini[$item_name]);
                }



                $ini[$name] = array('url' => $url,
                    'icon' => $icon,
                    'razdel' => $razdel,
                    'is_vip' => $is_vip,
                    'group' => $group
                );
                arraypos::setPosition($ini, $name, $position);
                if (ini::save(H . '/sys/ini/menu.' . $menu . '.ini', $ini, true)) {
                    $doc->msg(__('Изменения успешно приняты'));
                } else {
                    $doc->err(__('Ошибка при сохранении файла'));
                }
                header('Refresh: 1; url=?menu=' . urlencode($menu) . '&amp;' . passgen());
                $doc->ret(__('Меню "%s"', $menu), '?menu=' . urlencode($menu) . '&amp;' . passgen());
                $doc->ret(__('Список меню'), '?' . passgen());
                $doc->ret(__('Админка'), './?' . passgen());
                exit;
            }
        }



        if (isset($_GET['act']) && $_GET['act'] == 'delete') {
            $doc->title = __('Удаление пункта %s', $item_name);
            $smarty = new design();
            $smarty->assign('method', 'post');
            $smarty->assign('action', '?menu=' . urlencode($menu) . '&amp;item=' . urlencode($item_name) . '&amp;' . passgen());
            $elements = array();
            $elements[] = array('type' => 'captcha', 'session' => captcha::gen(), 'br' => 1);
            $elements[] = array('type' => 'submit', 'br' => 0, 'info' => array('name' => 'delete', 'value' => __('Удалить'))); // кнопка
            $smarty->assign('el', $elements);
            $smarty->display('input.form.tpl');
        } else {


            $form = new design ();
            $form->assign('method', 'post');
            $form->assign('action', '?menu=' . urlencode($menu) . '&amp;item=' . urlencode($item_name) . '&amp;' . passgen());
            $elements = array();

            $elements [] = array('type' => 'input_text', 'title' => __('Название'), 'br' => 1, 'info' => array('name' => 'name', 'value' => $item_name));
            $elements [] = array('type' => 'input_text', 'title' => __('Позиция'), 'br' => 1, 'info' => array('name' => 'position', 'value' => arraypos::getPosition($m_obj->menu_arr, $item_name)));
            $elements [] = array('type' => 'input_text', 'title' => __('Ссылка'), 'br' => 1, 'info' => array('name' => 'url', 'value' => $item['url']));


            $icons = (array) glob(H . '/sys/images/icons/*.png');
            $options = array();
            $options[] = array('', '[' . __('Отсутствует') . ']');
            foreach ($icons as $icon_path) {
                $icon = str_replace(H . '/sys/images/icons/', '', filesystem::unixpath($icon_path));
                $options[] = array($icon, $icon, $icon == @$item['icon']);
            }
            $elements[] = array('type' => 'select', 'br' => 1, 'title' => __('Иконка'), 'info' => array('name' => 'icon', 'options' => $options));

            $elements [] = array('type' => 'checkbox', 'br' => 1, 'info' => array('value' => 1, 'checked' => @$item['razdel'], 'name' => 'razdel', 'text' => __('Разделитель')));
            $elements [] = array('type' => 'checkbox', 'br' => 1, 'info' => array('value' => 1, 'checked' => @$item['is_vip'], 'name' => 'is_vip', 'text' => __('Только для VIP')));

            $options = array();
            foreach ($groups as $group => $value) {
                $options[] = array($group, $value['name'], $group == @$item['group']);
            }
            $elements[] = array('type' => 'select', 'br' => 1, 'title' => __('Для группы (и выше)') . '*', 'info' => array('name' => 'group', 'options' => $options));


            $elements[] = array('type' => 'text', 'value' => '* ' . __('Регулируется только отображение ссылки'), 'br' => 1);
            $elements [] = array('type' => 'submit', 'br' => 0, 'info' => array('name' => 'save', 'value' => __('Применить'))); // кнопка
            $form->assign('el', $elements);
            $form->display('input.form.tpl');
        }




        $doc->ret(__('Меню "%s"', $menu), '?menu=' . urlencode($menu) . '&amp;' . passgen());
        $doc->ret(__('Список меню'), '?' . passgen());
        $doc->ret(__('Админка'), './?' . passgen());
        exit;
    }

    if (isset($_GET['item_add'])) {
        $doc->title = __('Новый пункт меню %s', $menu);
        if (!empty($_POST['create'])) {
            $name = text::input_text(@$_POST['name']);
            $url = text::input_text(@$_POST['url']);
            $icon = text::input_text(@$_POST['icon']);
            $razdel = (int) !empty($_POST['razdel']);
            $is_vip = (int) !empty($_POST['is_vip']);
            $group = (int) @$_POST['group'];
            $position = (int) @$_POST['position'];
            if (empty($name)) {
                $doc->err(__('Название не может быть пустым'));
            } elseif (isset($m_obj->menu_arr[$name])) {
                $doc->err(__('Выбранное название меню уже занято'));
            } else {
                $ini = ini::read(H . '/sys/ini/menu.' . $menu . '.ini', true);


                $ini[$name] = array('url' => $url,
                    'icon' => $icon,
                    'razdel' => $razdel,
                    'is_vip' => $is_vip,
                    'group' => $group
                );
                arraypos::setPosition($ini, $name, $position);
                if (ini::save(H . '/sys/ini/menu.' . $menu . '.ini', $ini, true)) {
                    $doc->msg(__('Изменения успешно приняты'));
                } else {
                    $doc->err(__('Ошибка при сохранении файла'));
                }
                header('Refresh: 1; url=?menu=' . urlencode($menu) . '&amp;' . passgen());
                $doc->ret(__('Меню "%s"', $menu), '?menu=' . urlencode($menu) . '&amp;' . passgen());
                $doc->ret(__('Список меню'), '?' . passgen());
                $doc->ret(__('Админка'), './?' . passgen());
                exit;
            }
        }







        $form = new design ();
        $form->assign('method', 'post');
        $form->assign('action', '?menu=' . urlencode($menu) . '&amp;item_add&amp;' . passgen());
        $elements = array();

        $elements [] = array('type' => 'input_text', 'title' => __('Название'), 'br' => 1, 'info' => array('name' => 'name'));
        $elements [] = array('type' => 'input_text', 'title' => __('Позиция'), 'br' => 1, 'info' => array('name' => 'position', 'value' => count($m_obj->menu_arr) + 1));
        $elements [] = array('type' => 'input_text', 'title' => __('Ссылка'), 'br' => 1, 'info' => array('name' => 'url', 'value' => 'http://'));


        $icons = (array) glob(H . '/sys/images/icons/*.png');
        $options = array();
        $options[] = array('', '[' . __('Отсутствует') . ']');
        foreach ($icons as $icon_path) {
            $icon = str_replace(H . '/sys/images/icons/', '', filesystem::unixpath($icon_path));
            $options[] = array($icon, $icon);
        }
        $elements[] = array('type' => 'select', 'br' => 1, 'title' => __('Иконка'), 'info' => array('name' => 'icon', 'options' => $options));

        $elements [] = array('type' => 'checkbox', 'br' => 1, 'info' => array('value' => 1, 'name' => 'razdel', 'text' => __('Разделитель')));
        $elements [] = array('type' => 'checkbox', 'br' => 1, 'info' => array('value' => 1, 'name' => 'is_vip', 'text' => __('Только для VIP')));

        $options = array();
        foreach ($groups as $group => $value) {
            $options[] = array($group, $value['name']);
        }
        $elements[] = array('type' => 'select', 'br' => 1, 'title' => __('Для группы (и выше)') . '*', 'info' => array('name' => 'group', 'options' => $options));
        $elements[] = array('type' => 'text', 'value' => '* ' . __('Регулируется только отображение ссылки'), 'br' => 1);
        $elements [] = array('type' => 'submit', 'br' => 0, 'info' => array('name' => 'create', 'value' => __('Создать'))); // кнопка
        $form->assign('el', $elements);
        $form->display('input.form.tpl');

        $doc->ret(__('Меню "%s"', $menu), '?menu=' . urlencode($menu) . '&amp;' . passgen());
        $doc->ret(__('Список меню'), '?' . passgen());
        $doc->ret(__('Админка'), './?' . passgen());
        exit;
    }



    if (isset($_GET['sortable'])) {

        $ini = ini::read(H . '/sys/ini/menu.' . $menu . '.ini', true);
        $doc->clean();

        //echo $_POST['sortable'];
        $sortable = explode(',', $_POST['sortable']);

        foreach ($sortable as $position => $key) {
            //$key = base64_decode($key);
            // echo "$position $key\n";
            arraypos::setPosition($ini, $key, $position + 1);
        }

        header('Content-type: application/json');
        if (ini::save(H . '/sys/ini/menu.' . $menu . '.ini', $ini, true)) {
            echo json_encode(array('result' => 1, 'description' => __('Порядок пунктов меню успешно сохранен')));
        } else {
            echo json_encode(array('result' => 0, 'description' => __('Не удалось сохранить порядок пунктов в меню')));
        }


        exit;
    }



    if (isset($_GET['up']) || isset($_GET['down'])) {
        $ini = ini::read(H . '/sys/ini/menu.' . $menu . '.ini', true);

        if (isset($_GET['up'])) {
            $item_name = $_GET['up'];
            if (misc::array_key_move($ini, $item_name, - 1)) {
                $doc->msg(__('Пункт "%s" успешно перемещен вверх', $item_name));
            } else {
                $doc->err(__('Пункт "%s" уже находится вверху', $item_name));
            }
        }

        if (isset($_GET['down'])) {
            $item_name = $_GET['down'];
            if (misc::array_key_move($ini, $item_name, 1)) {
                $doc->msg(__('Пункт "%s" успешно перемещен вниз', $item_name));
            } else {
                $doc->err(__('Пункт "%s" уже находится внизу', $item_name));
            }
        }
        if (ini::save(H . '/sys/ini/menu.' . $menu . '.ini', $ini, true)) {
            $doc->msg(__('Изменения успешно приняты'));
        } else {
            $doc->err(__('Ошибка при сохранении файла'));
        }
        $m_obj = new menu($menu);
    }


    $listing = new listing();

    $position = 0;
    foreach ($m_obj->menu_arr as $name => $item) {
        $position++;

        $post = $listing->post();
        $post->id = $name;
        $post->url = '?menu=' . urlencode($menu) . '&amp;item=' . urlencode($name);
        $post->title = for_value($name);

        $post->icon(@$item['icon']);

        $post->action('up', '?menu=' . urlencode($menu) . '&amp;up=' . urlencode($name) . '&amp;' . passgen());
        $post->action('down', '?menu=' . urlencode($menu) . '&amp;down=' . urlencode($name) . '&amp;' . passgen());
        $post->action('delete', '?menu=' . urlencode($menu) . '&amp;item=' . urlencode($name) . '&amp;act=delete&amp;' . passgen());



        if (empty($item['razdel'])) {
            $post->content = __('Ссылка') . ": $item[url]\n";
            if (!empty($item['icon'])) {
                $icon = array('size' => 'small', 'src' => '/sys/images/icons/' . $item['icon']);
            }
        } else {
            $post->content = "[b]" . __('Разделитель') . "[/b]\n";
            $post->hightlight = true;
        }

        if (!empty($item['for_vip'])) {
            $post->content .= __('Только для [b]VIP[/b]-пользователей') . "\n";
        }

        if (!empty($item['group'])) {
            $post->content .= __('Только для группы [b]%s[/b] (%s)', groups::name($item['group']), $item['group']) . " \n";
        }
        $post->content = output_text($post->content);
    }


    $listing->sortable = '?sortable&amp;menu=' . urlencode($menu);
    $listing->display(__('Меню пусто'));

    $doc->act(__('Добавить пункт'), '?menu=' . urlencode($menu) . '&amp;item_add&amp;' . passgen());


    $doc->ret(__('Список меню'), '?' . passgen());
    $doc->ret(__('Админка'), './?' . passgen());
    exit;
}


$listing = new listing();
foreach ($menus as $menu) {
    $post = $listing->post();
    $post->title = for_value($menu);
    $post->url = '?menu=' . urlencode($menu);
    $post->icon('menu.editor');
}
$listing->display(__('Нет меню для редактирования'));


$doc->ret(__('Админка'), './?' . passgen());
?>