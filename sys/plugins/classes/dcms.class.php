<?php

class dcms extends browser {

    protected $_counters = array();
    protected $_data = array();
    var $license;

    function __construct() {
        parent::__construct();
        // загрузка настроек
        $this->_load_settings();
    }

    /**
     * рассылка системных сообщений
     * @param string $mess
     * @param integer $group_min
     */
    public function distribution($mess, $group_min = 2) {
        $q = mysql_query("SELECT `id` FROM `users` WHERE `group` >= '" . intval($group_min) . "'");
        while ($ank = mysql_fetch_assoc($q)) {
            $ank = new user($ank['id']);
            $ank->mess($mess);
        }
    }

    /**
     * Запись действий администратора или системы
     * @global type $user
     * @param type $module
     * @param type $description
     * @param type $is_system
     * @return type
     */
    public function log($module, $description, $is_system = false) {
        $id_user = 0;

        if (!$is_system) {
            global $user;
            $id_user = $user->id;
        }

        return mysql_query("INSERT INTO `action_list_administrators` (`id_user`, `time`, `module`, `description`)
VALUES ('$id_user', '" . TIME . "', '" . my_esc($module) . "', '" . my_esc($description) . "')");
    }

    public function __call($name, $arg) {
        switch ($name) {
            case 'count':return $this->_count($arg[0]);
            default:return false;
        }
    }

    public function __get($name) {
        switch ($name) {
            case 'subdomain_main': return $this->_subdomain_main();
                break;
            case 'browser_type': return $this->_browser_type();
                break;
            case 'browser_id': return $this->browser_id();
                break;
            case 'items_per_page': return $this->_data['items_per_page_' . $this->browser_type];
                break;
            case 'img_max_width':return $this->_data['img_max_width_' . $this->browser_type];
                break;
            case 'widget_items_count':return $this->_data['widget_items_count_' . $this->browser_type];
                break;
            case 'theme':return $this->_data['theme_' . $this->browser_type];
                break;
            default:return empty($this->_data[$name]) ? false : $this->_data[$name];
        }
    }

    public function __set($name, $value) {
        switch ($name) {
            case 'items_per_page': $name .= '_' . $this->browser_type;
                break;
            case 'theme': $name .= '_' . $this->browser_type;
                break;
            case 'img_max_width': $name .= '_' . $this->browser_type;
                break;            
            case 'widget_items_count': $name .= '_' . $this->browser_type;
                break;
        }
        $this->_data[$name] = $value;
        return true;
    }

    protected function _subdomain_main() {
        $domain = preg_replace('/^(wap|pda|web|www|i|touch|itouch)\./ui', '', $_SERVER['HTTP_HOST']);
        return $domain;
    }

    protected function _browser_type() {
        if ($this->subdomain_wap_enable) {
            if (0 === strpos($_SERVER['HTTP_HOST'], $this->subdomain_wap . '.')) {
                return 'wap';
            }
        }
        if ($this->subdomain_pda_enable) {
            if (0 === strpos($_SERVER['HTTP_HOST'], $this->subdomain_pda . '.')) {
                return 'pda';
            }
        }
        if ($this->subdomain_itouch_enable) {
            if (0 === strpos($_SERVER['HTTP_HOST'], $this->subdomain_itouch . '.')) {
                return 'itouch';
            }
        }
        if ($this->subdomain_web_enable) {
            if (0 === strpos($_SERVER['HTTP_HOST'], $this->subdomain_web . '.')) {
                return 'web';
            }
        }


        return $this->browser_type_auto;
    }

    // счетчики
    protected function _count($item) {
        if (!isset($this->_counters[$item])) {
            switch ($item) {
                // кол-во пользователей
                case 'users': $this->_counters[$item] = mysql_result(mysql_query("SELECT COUNT(*) FROM `users`"), 0);
                    break;
                // пользователи онлайн
                case 'users_online': $this->_counters[$item] = mysql_result(mysql_query("SELECT COUNT(*) FROM `users_online`"), 0);
                    break;
                // гости онлайн
                case 'guest_online': $this->_counters[$item] = mysql_result(mysql_query("SELECT COUNT(*) FROM `guest_online` WHERE `conversions` >= '5'"), 0);
                    break;

                default:$this->_counters[$item] = false;
            }
        }

        return $this->_counters[$item];
    }

    /**
     * Загрузка настроек
     */
    protected function _load_settings() {
        $settings_default = ini::read(H . '/sys/inc/settings.default.ini', true) OR die('Невозможно загрузить файл настроек по-умолчанию');
        if (!$settings = ini::read(H . '/sys/ini/settings.ini')) {
            // если установки небыли загружены, но при этом есть файл установки, то переадресуем на него
            if (file_exists(H . '/install/index.php')) {
                header("Location: /install/");
                exit;
            } else
                exit('Файл настроек не может быть загружен');
        }
        $this->_data = array_merge($settings_default['DEFAULT'], $this->_data, $settings, $settings_default['REPLACE']);
    }

    /**
     * Сохранение настроек
     * @return boolean
     */
    public function save_settings() {
        return ini::save(H . '/sys/ini/settings.ini', $this->_data);
    }

}

?>