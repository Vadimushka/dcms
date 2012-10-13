<?php

include_once '../sys/inc/start.php';
dpanel::check_access();
$doc = new document(6);
$doc->title = __('Загрузка таблиц');

$tables_exists = new tables();
$table_files = (array) glob(H . '/sys/preinstall/base.create.*.ini');
$tables = array();
foreach ($table_files as $table_file) {
    preg_match('#base.create\.(.+)\.ini#ui', $table_file, $m);
    $tables[] = $m[1];
}

if (!empty($_POST)) {
    foreach ($_POST as $table => $val) {
        // echo $table."<br />";
        if (!$val)
            continue;
        if (in_array($table, $tables)) {
            if (function_exists('set_time_limit'))
                set_time_limit(600);

            if (!empty($_POST['load'])) {
                if (!is_file(H . '/sys/preinstall/base.create.' . $table . '.ini')) {
                    continue;
                }

                $tab = new table_structure(H . '/sys/preinstall/base.create.' . $table . '.ini');
                $sql = $tab->getSQLQueryCreate();
                // если такая таблица уже существует, то переименовываем ее
                if (in_array($table, $tables_exists->tables)) {
                    mysql_query("ALTER TABLE `" . my_esc($table) . "` RENAME `" . '~' . TIME . '~' . my_esc($table) . "`");
                    $doc->msg(__('Существующая таблица "%s" была переименована', $table));
                }

                if (mysql_query($sql)) {
                    $doc->msg(__('Запрос на создание таблицы "%s" успешно выполнен', $table));
                    $tables_exists = new tables();
                    if (in_array($table, $tables_exists->tables))
                        $doc->msg(__('Таблица "%s" успешно создана', $table));
                    else
                        $doc->err(__('Таблица "%s" не создана', $table));
                }
            }
        }
    }
}



$listing = new listing();
foreach ($tables as $table) {
    $ch = $listing ->checkbox();
    $ch -> name = $table;
    $ch -> title = $table;
    $ch -> checked = !in_array($table, $tables_exists->tables);    
}

echo "<form method='post' action='?" . passgen() . "'>";
$listing->display(__('Таблицы отсутствуют'));
echo "* " . __('При совпадении имени загружаемой таблицы с существующей, существующая таблица будет переименована') . "<br />";
echo "** " . __('Проверяются файлы sys/preinstall/base.create.[имя таблицы].ini') . "<br />";
echo "<input type='submit' name='load' value='" . __('Загрузить') . "' /></form>";

$doc->ret(__('Админка'), './');
?>
