<?php

class design extends native_templating {

    public $theme;

    function __construct() {
        parent::__construct();
        global $dcms, $user_language_pack, $user, $probe_theme;
        static $theme = false;
        if ($theme === false) {
            if (!empty($probe_theme) && themes::exists($probe_theme)){
                $theme = themes::getConfig($probe_theme);
            }elseif (themes::exists($user->theme)) {
                // пользовательская тема оформления
                $theme = themes::getConfig($user->theme);
            } elseif (themes::exists($dcms->theme)) {
                // системная тема оформления
                $theme = themes::getConfig($dcms->theme);
            } elseif (($themes = themes::getList($dcms->browser_type))) {
                // тема оформления для типа браузера
                $theme = current($themes);
            } else {
                // любая тема оформления
                echo '<!--'.print_r(themes::getList(),1).'-->';
                $theme = current(themes::getList());
                if (!$theme)
                    die('Не найдено ни одной совместимой темы оформления');
            }
        }
        
        $this->theme = $theme;

        // папка шаблонов
        $this->dir_template = H . '/sys/themes/' . $theme['dir'] . '/tpl/';

        // системные переменные
        $this->assign('theme', $theme);        
        $this->assign('dcms', $dcms); // !!! под вопросом удаления
        $this->assign('copyright', $dcms->copyright, 2);
        $this->assign('lang', $user_language_pack);
        $this->assign('user', $user);
        $this->assign('path', '/sys/themes/' . $theme['dir']);
    }

    function img_max_width() {
        global $dcms;
        return min($this->theme['img_width_max'], $dcms->img_max_width);
    }

    function getIconPath($name) {
        if (!$name)
            return null;
        return $this->theme['icons'] . '/' . basename($name, '.png') . '.png';
    }

}

?>