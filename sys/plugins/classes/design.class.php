<?php

include_once H . '/sys/plugins/smarty/Smarty.class.php';

class design extends Smarty {

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
                $theme = $themes[0];
            } else {
                // любая тема оформления
                $themes = themes::getList();
                $theme = $themes[0];
            }
        }


        $this->theme = $theme;

        // папки темы оформления
        $this->template_dir = H . '/sys/themes/' . $theme['dir'] . '/tpl/';
        $this->compile_dir = H . '/sys/themes/' . $theme['dir'] . '/tpl_c/';
        $this->config_dir = H . '/sys/themes/' . $theme['dir'] . '/tpl_conf/';
        $this->cache_dir = H . '/sys/themes/' . $theme['dir'] . '/tpl_cache/';

        // конфигурация
        $this->setCaching(Smarty::CACHING_OFF);
        $this->setCompileCheck(true);
        $this->allow_php_tag = true;
        $this->error_reporting = false;

        // системные переменные
        $this->assign('URL', URL);
        $this->assign('theme', $theme);        
        $this->assignByRef('dcms', $dcms);
        $this->assignByRef('lang', $user_language_pack);
        $this->assignByRef('user', $user);
        $this->assign('SESSION_NAME', SESSION_NAME);
        $this->assign('SESS', SESS);
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