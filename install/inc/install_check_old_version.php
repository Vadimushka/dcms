<?php

class install_check_old_version {

    var $tables;
    var $old_version = false;

    function __construct() {
        db_connect();

        $this->tables = \Dcms\Helpers\DbStructure\DbStructure::getAllTables(Db::me());

        if (
                in_array('user', $this->tables) &&
                in_array('mail', $this->tables) &&
                in_array('forum_f', $this->tables) &&
                in_array('forum_r', $this->tables) &&
                in_array('forum_t', $this->tables) &&
                in_array('forum_p', $this->tables) &&
                in_array('ban', $this->tables) &&
                in_array('rekl', $this->tables) &&
                in_array('news', $this->tables)
        )
            $this->old_version = true;
    }

    function actions() {
        global $options;

        if (!$this->old_version || empty($_POST['convert_old_version']))
            $options['new_base'] = true;
        else
            $options['convert_old_version'] = true;

        return true;
    }

    function form() {
        if ($this->old_version) {
            echo '<label><input type="checkbox" checked="checked" value="1" name="convert_old_version" />' . __('Импортировать данные из старой версии') . '</label>';
        } else {
            echo __("Таблицы от старой версии движка не обнаружены");
        }

        return true;
    }

}