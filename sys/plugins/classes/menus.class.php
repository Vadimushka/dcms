<?php

abstract class menus
{

    /**
     * Получение всех доступных меню
     * @return menu[]
     */
    static public function getAllMenus()
    {
        $menus = array();

        // получение всех пунктов меню из базы
        $db = db::me();
        $res = $db->query('SELECT * FROM `menu`');
        $all_menu = $res->fetchAll();

        // получение ключей меню
        $menu_keys = array();
        foreach ($all_menu as $menu_item) {
            if (!in_array($menu_item['menu_key'], $menu_keys)){
                $menu_keys[] = $menu_item['menu_key'];
            }
        }

        // выборка пунктов меню на основе ключей меню.
        foreach ($menu_keys as $menu_key) {
            $items = array();
            foreach($all_menu AS $menu_item){
                if ($menu_item['menu_key'] === $menu_key){
                    $items[] = $menu_item;
                }
            }
            $menus[] = new menu($items);
        }

        return $menus;
    }
}