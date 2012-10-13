<?php

// класс для работы с пользователем
class user extends plugins {

    protected $_update = array();
    protected $_data = array();

    function __construct($id) {
        $this->_user($id);
    }

    /**
     * инициализация данных пользователя
     * @global type $dcms
     * @staticvar array $cache
     * @param type $id
     * @return boolean
     */
    protected function _user($id) {
        $this->_data ['id'] = false;
        $this->_data ['sex'] = 1;
        $this->_data ['group'] = 0;

        if ($id === 0) {
            global $dcms;
            // для системных уведомлений
            $this->_data ['id'] = 0;
            $this->_data ['login'] = '[' . $dcms->system_nick . ']';
            $this->_data ['group'] = 6;
            $this->_data ['description'] = __('Системный бот. Создан для уведомлений.');
            return true;
        }

        static $cache = array();
        $id = (int) $id;

        if (isset($cache [$id])) {
            $this->_data = $cache [$id];
        } else {
            $q = mysql_query("SELECT * FROM `users` WHERE `id` = '$id' LIMIT 1");
            if (mysql_num_rows($q)) {
                $this->_data = mysql_fetch_assoc($q);
            }
            $cache [$id] = $this->_data;
        }
    }

    /**
     * проверка бана пользователя
     * @staticvar array $is_ban
     * @return boolean
     */
    protected function _is_ban() {
        static $is_ban = array();

        if (!isset($is_ban [$this->_data ['id']])) {
            $is_ban [$this->_data ['id']] = mysql_result(mysql_query("SELECT COUNT(*) FROM `ban` WHERE `id_user` = '{$this->_data['id']}' AND `time_start` < '" . TIME . "' AND (`time_end` is NULL OR `time_end` > '" . TIME . "')"), 0);
        }

        return !empty($is_ban [$this->_data ['id']]);
    }

    /**
     * проверка полного (запрет навигации) бана пользователя
     * @staticvar array $is_ban_full
     * @return boolean
     */
    protected function _is_ban_full() {
        static $is_ban_full = array();

        if (!isset($is_ban_full [$this->_data ['id']])) {
            $is_ban_full [$this->_data ['id']] = mysql_result(mysql_query("SELECT COUNT(*) FROM `ban` WHERE `id_user` = '{$this->_data['id']}' AND `access_view` = '0' AND `time_start` < '" . TIME . "' AND (`time_end` is NULL OR `time_end` > '" . TIME . "')"), 0);
        }

        return !empty($is_ban_full [$this->_data ['id']]);
    }

    /**
     * проверяет, находится ли пользователь сейчас в онлайне
     * @staticvar array $online
     * @param integer $id_user
     * @return boolean
     */
    protected function _is_online($id_user) {
        static $online = false;
        if ($online === false) {
            $online = array();
            $q = mysql_query("SELECT `id_user` FROM `users_online`");
            while ($on = mysql_fetch_assoc($q)) {
                $online[$on ['id_user']] = true;
            }
        }

        return isset($online[$id_user]);
    }

    /**
     * Проверка на возможность писать сообщения
     * @global dcms $dcms
     * @return boolean
     */
    protected function _is_writeable() {
        if ($this->_is_ban())
            return false;       

        global $dcms;
        if (!$dcms->user_write_limit_hour) {
            // ограничение не установлено
            return true;
        } elseif ($this->_data['group'] >= 2) {
            // пользователь входит в состав администрации
            return true;
        } elseif ($this->_data['reg_date'] < TIME - $dcms->user_write_limit_hour * 3600) {
            // пользователь преодолел ограничение
            return true;
        } else {
            return false;
        }
    }

    function __get($n) {
        global $dcms;
        switch ($n) {
            case 'language' :
                return empty($this->_data ['language']) ? $dcms->language : $this->_data ['language'];
            case 'is_writeable' :
                return $this->_is_writeable();
            case 'is_ban' :
                return $this->_is_ban();
            case 'is_ban_full' :
                return $this->_is_ban_full();
            case 'online' :
                return (bool) (@$this->_data ['last_visit'] > TIME - SESSION_LIFE_TIME);
            case 'group_name' :
                return groups::name($this->_data ['group']);
            case 'items_per_page' :
                return !empty($this->_data ['items_per_page_' . $dcms->browser_type]) ? $this->_data ['items_per_page_' . $dcms->browser_type] : $dcms->items_per_page;
            case 'theme' :
                return @$this->_data ['theme_' . $dcms->browser_type];
            case 'nick' :
                return @$this->nick();
            default :
                return !isset($this->_data [$n]) ? false : $this->_data [$n];
        }
    }

    function __set($n, $v) {
        if (empty($this->_data ['id']))
            return false;
        global $dcms;
        switch ($n) {
            case 'theme' :
                $n .= '_' . $dcms->browser_type;
                break;
            case 'items_per_page' :
                $n .= '_' . $dcms->browser_type;
                break;
        }

        if (isset($this->_data [$n])) {
            $this->_data [$n] = $v;
            $this->_update [$n] = $v;
        } else {
            trigger_error("Поле $n не существует");
        }
    }

    function __destruct() {
        if ($this->_update) {
            $sql = array();
            foreach ($this->_update as $key => $value) {
                $sql [] = "`" . my_esc($key) . "` = '" . my_esc($value) . "'";
            }
            mysql_query("UPDATE `users` SET " . implode(', ', $sql) . " WHERE `id` = '" . $this->_data ['id'] . "' LIMIT 1");
        }
    }

}

?>