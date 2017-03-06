<?php

include_once '../sys/inc/start.php';
dpanel::check_access();
$doc = new document(6);
$doc->title = __('Загрузка таблиц');

$dbTables = \Dcms\Helpers\DbStructure\DbStructure::getAllTables(Db::me());

$table_files = (array)glob(H . '/sys/preinstall/table.*.structure.json');
$tables = array();
foreach ($table_files as $table_file) {
    preg_match('#table\.(.+)\.structure\.json#ui', $table_file, $m);
    $tables[] = $m[1];
}

if (!empty($_POST)) {
    foreach ($_POST as $table => $val) {
        // echo $table."<br />";
        if (!$val) {
            continue;
        }
        if (in_array($table, $tables)) {
            if (function_exists('set_time_limit')) {
                set_time_limit(600);
            }

            if (!empty($_POST['load'])) {
                if (!is_file(H . '/sys/preinstall/table.' . $table . '.structure.json')) {
                    continue;
                }

                $tableStructure = new \Dcms\Helpers\DbStructure\DbStructureTable();
                $tableStructure->loadFromJsonFile(H . '/sys/preinstall/table.' . $table . '.structure.json');

                $sql = $tableStructure->getSQLCreate();
                // если такая таблица уже существует, то переименовываем ее
                if (in_array($table, $dbTables)) {
                    //Не знаю как это сделать красиво
                    $db->query("ALTER TABLE `$table` RENAME `" . '~' . TIME . '~' . $table . '`');
                    $doc->msg(__('Существующая таблица "%s" была переименована', $table));
                }

                if ($db->query($sql)) {
                    $doc->msg(__('Запрос на создание таблицы "%s" успешно выполнен', $table));
                    if (in_array($table, $dbTables)) {
                        $doc->msg(__('Таблица "%s" успешно создана', $table));
                    } else {
                        $doc->err(__('Таблица "%s" не создана', $table));
                    }
                }
            }
        }
    }
}

$listing = new listing();
foreach ($tables as $table) {
    $ch = $listing->checkbox();
    $ch->name = $table;
    $ch->title = $table;
    $ch->checked = !in_array($table, $dbTables);
}

if ($listing->count()) {
    $form = new form('?' . passgen());
    $form->html($listing->fetch());
    $form->bbcode('[notice] ' . __('При совпадении имени загружаемой таблицы с существующей, существующая таблица будет переименована.'));
    $form->bbcode('[notice] ' . __('Данная операция может повлечь потерю данных. Если вы не уверены в своих действиях, лучше покиньте данную страницу.'));
    $form->button(__('Загрузить'), 'load');
    $form->display();
} else {
    $listing->display(__('Данные о структуре таблиц отсутствуют'));
}

$doc->ret(__('Админка'), './');
