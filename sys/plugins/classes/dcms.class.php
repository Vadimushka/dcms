<?php

/**
 * Базовый класс системы. Объек хранится в глобальной переменной $dcms
 */
class dcms {

    protected $_data = array();
    public $db;

    function __construct() {
        // загрузка настроек
        $this->_load_settings();
    }

    /**
     * рассылка системных сообщений
     * @param string $mess
     * @param integer $group_min
     */
    public function distribution($mess, $group_min = 2) {
        $group_min = (int) $group_min;
        $q = $this->db->prepare("SELECT `id` FROM `users` WHERE `group` >= ?");
        $q->execute(Array($group_min));
        $users = array();
        while ($ank_ids = $q->fetch()) {
            $users[] = $ank_ids['id'];
        }
        new user($users); // предзагрузка данных пользователей из базы

        foreach ($users as $id_user) {
            $ank = new user($id_user);
            $ank->mess($mess);
        }
    }

    /**
     * Запись действий администратора или системы
     * @global \user $user
     * @param string $module Название модуля
     * @param string $description Описание действия
     * @param boolean $is_system Если сестемное действие
     * @return type
     */
    public function log($module, $description, $is_system = false) {
        $id_user = 0;

        if (!$is_system) {
            global $user;
            $id_user = $user->id;
        }

        $res = $this->db->prepare("INSERT INTO `action_list_administrators` (`id_user`, `time`, `module`, `description`) VALUES (?, ?, ?, ?)");
        $res->execute(Array($id_user, TIME, $module, $description));
        return true;
    }

    public function __get($name) {
        switch ($name) {
            case 'salt_user':
                return $this->salt . @$_SERVER['HTTP_USER_AGENT'];
                break;
            case 'ip_long':
                return browser::getIpLong();
                break;
            case 'subdomain_main':
                return $this->_subdomain_main();
                break;
            case 'browser_name':
                return browser::getName();
                break;
            case 'browser_type':
                return $this->_browser_type();
                break;
            case 'browser_type_auto':
                return browser::getType();
                break;
            case 'browser_id':
                return $this->_browser_id();
                break;
            case 'items_per_page':
                return $this->_data['items_per_page_' . $this->browser_type];
                break;
            case 'img_max_width':
                return $this->_data['img_max_width_' . $this->browser_type];
                break;
            case 'widget_items_count':
                return $this->_data['widget_items_count_' . $this->browser_type];
                break;
            case 'theme':
                return $this->_data['theme_' . $this->browser_type];
                break;
            default:
                return empty($this->_data[$name]) ? false : $this->_data[$name];
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

    /**
     * Тип браузера
     * @return string
     */
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

    protected function _browser_id() {
        static $browser_id = false;

        if ($browser_id === false) {
            $q = $this->db->prepare("SELECT * FROM `browsers` WHERE `name` = ? LIMIT 1");
            $q->execute(Array(browser::getName()));
            if ($row = $q->fetch()) {
                $browser_id = $row['id'];
            } else {
                $q = $this->db->prepare("INSERT INTO `browsers` (`type`, `name`) VALUES (?,?)");
                $q->execute(Array(browser::getType(), browser::getName()));
                $browser_id = $this->db->lastInsertId();
            }
        }
        return $browser_id;
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
            }
            else
                exit('Файл настроек не может быть загружен');
        }
        $this->_data = array_merge($settings_default['DEFAULT'], $this->_data, $settings, $settings_default['REPLACE']);
    }

    /**
     * Сохранение настроек
     * @return boolean
     */
    public function save_settings($doc = false) {
        $result = ini::save(H . '/sys/ini/settings.ini', $this->_data);

        if (is_a($doc, 'document')) {
            if ($result)
                $doc->msg(__('Настройки успешно сохранены'));
            else
                $doc->err(__('Нет прав на запись в файл настроек'));
        }

        return $result;
    }

}