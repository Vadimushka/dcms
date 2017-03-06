<?php

class install_load_sql {

    var $tables;
    var $errors;

    function __construct() {
        db_connect();
        $this->tables = \Dcms\Helpers\DbStructure\DbStructure::getAllTables(Db::me());
        $this->errors = &$_SESSION['install_load_sql_errors'];
    }

    function actions() {
        global $options;
        $files_sql = array();
        if (!empty($_POST['load_data']) && !empty($options['new_base']))
            $files_sql = (array)glob(H . '/sys/preinstall/table.*.data.sql');

        $files_ini = (array)glob(H . '/sys/preinstall/table.*.structure.json');

        if (empty($_SESSION['rename_prefix']))
            $_SESSION['rename_prefix'] = '~' . TIME . '~';

        foreach ($this->tables as $table) {
            if ($table{0} == '~')
                continue;
            DB::me()->query("ALTER TABLE `$table` RENAME `" . $_SESSION['rename_prefix'] . "$table`");
        }

        foreach ($files_ini as $file) {
            $tab = new \Dcms\Helpers\DbStructure\DbStructureTable();
            $tab->loadFromJsonFile($file);
            $sql = $tab->getSQLCreate();

            DB::me()->query("DROP TABLE IF EXISTS `{$tab->Properties->Name}`");

            if (!DB::me()->query($sql)) {
                $this->errors[] = array($sql, DB::me()->errorInfo()[2]);
            }

        }
        // exit;
        foreach ($files_sql as $file) {
            $sqls = sql_parser::getQueriesFromFile($file);
            foreach ($sqls as $sql) {
                if (!DB::me()->query($sql)) {
                    $this->errors[] = array($sql, DB::me()->errorInfo()[2]);
                }
            }
        }


        return !$this->errors;
    }

    function form() {
        echo __('На данном этапе создадутся необходимые для работы движка таблицы в базе данных. Если в базе уже находятся какие-либо таблицы, к ним будет добавлен префикс с временной меткой');

        global $options;
        if (!empty($options['new_base'])) {
            $files = glob(H . '/sys/preinstall/table.*.data.sql');

            if ($files)
                echo '<br /><label><input type="checkbox" checked="checked" value="1" name="load_data" />' . __('Загрузить содержимое таблиц') . '</label>';
        }
        if ($this->errors) {
            echo "<br />" . __("При выполнении SQL запросов возникли ошибки") . "<br />";

            foreach ($this->errors as $error) {
                echo "<br /><pre>" . print_r($error, true) . "</pre>";
            }
        }

        $this->errors = array();

        return true;
    }

}