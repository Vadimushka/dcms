<?php

class pages {

    public $pages = 0; // количество страниц
    public $posts = 0; // количество пунктов всего
    public $items_per_page = 10; // количество пунктов на одну страницу
    public $this_page = 1; // текущая страница

    function pages($posts = 0) {
        global $user;
        $this->items_per_page = $user->items_per_page;
        $this->posts = $posts;
    }

    // получение текущей страницы
    function this_page() {
        $this->count();
        if (isset($_GET['page'])) {
            if ($_GET['page'] == 'end') {
                $this->this_page = $this->pages;
            } elseif (is_numeric($_GET['page'])) {
                $this->this_page = max(1, min($this->pages, intval($_GET['page'])));
            } else {
                $this->this_page = 1;
            }
        } elseif (isset($_GET['postnum'])) {
            if ($_GET['postnum'] == 'end') {
                $this->this_page = $this->pages;
            } elseif (is_numeric($_GET['postnum'])) {
                $this->this_page = max(1, min($this->pages, ceil($_GET['postnum'] / $this->items_per_page)));
            } else {
                $this->this_page = 1;
            }
        } else {
            $this->this_page = 1;
        }
    }

    function limit() {
        return $this->my_start() . ', ' . $this->items_per_page;
    }

    // старт извлечения из базы
    function my_start() {
        return $this->items_per_page * ($this->this_page - 1);
    }

    // конец
    function end() {
        return $this->items_per_page * $this->this_page;
    }

    // пересчет количества страниц
    function count() {
        if (!$this->posts) {
            $this->pages = 1;
        } else {
            $this->pages = ceil($this->posts / $this->items_per_page);
        }
    }

    // вывод списка страниц
    function display($link) {
        if ($this->pages > 1) {
            $list = new design();
            $list->assign('link', $link);
            $list->assign('k_page', $this->pages);
            $list->assign('page', $this->this_page);
            $list->display('design.pages.tpl');
        }
    }

    function listing($link) {
        $this->display($link);
    }

    function __get($name) {
        switch ($name) {
            case 'limit':return $this->limit();
            case 'my_start':return $this->my_start();
            case 'this_page':return $this->this_page();
            case 'end':return $this->end();
        }
    }

}

?>