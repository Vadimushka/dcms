<?php

include_once '../sys/inc/start.php';
dpanel::check_access();
$doc = new document(6);
$doc->title = __('Обновление структуры таблиц');

$dbStructure = new \Dcms\Helpers\DbStructure\DbStructure();
$dbStructure->loadFromBase(Db::me());

$dbTables = [];
foreach ($dbStructure->Tables as $table) {
    $dbTables[] = $table->Properties->Name;
}

$table_files = (array)glob(H . '/sys/preinstall/table.*.structure.json');
$tables = array();
foreach ($table_files as $table_file) {
    preg_match('#table\.(.+)\.structure\.json#ui', $table_file, $m);
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
                if (!is_file(H . '/sys/preinstall/table.' . $table . '.structure.json'))
                    continue;

                $fTable = new \Dcms\Helpers\DbStructure\DbStructureTable();
                $fTable->loadFromJsonFile(H . '/sys/preinstall/table.' . $table . '.structure.json');

                // если такая таблица уже существует, то получаем запрос на ее изменение
                if (in_array($table, $dbTables)) {

                    $dbTable = new \Dcms\Helpers\DbStructure\DbStructureTable();
                    $dbTable->loadFromBase(DB::me(), $table);

                    $sql = $dbTable->getSQLChange($fTable);
                } else
                    $sql = $fTable->getSQLCreate();

                if ($db->query($sql)) {
                    $doc->msg(__('Запрос на изменение таблицы "%s" успешно выполнен', $table));
                    if (in_array($table, $dbTables))
                        $doc->msg(__('Таблица "%s" успешно изменена', $table));
                    else
                        $doc->err(__('Таблица "%s" не создана', $table));
                }
            }
        }
    }
}

$listing = new listing();

foreach ($tables as $table) {

    $checked = false;
    $sql = false;
    if (!in_array($table, $dbTables)) {
        // таблица не существует в базе, значит нужно создать
        $checked = true;
    } else {

        $dbTable = new \Dcms\Helpers\DbStructure\DbStructureTable();
        $dbTable->loadFromBase(DB::me(), $table);

        $fTable = new \Dcms\Helpers\DbStructure\DbStructureTable();
        $fTable->loadFromJsonFile(H . '/sys/preinstall/table.' . $table . '.structure.json');

        // если есть изменения, то обновляем
        if ($sql = $dbTable->getSQLChange($fTable))
            $checked = true;
    }

    $post = empty($sql) ? '' : '<pre>' . text::toOutput($sql) . '</pre>';
    if ($post) {
        $ch = $listing->checkbox();
        $ch->checked = $checked;
        $ch->name = $table;
        $ch->title = $table;
        $ch->content = $post;
    }
}

if ($listing->count()) {
    $form = new form('?' . passgen());
    $form->html($listing->fetch());
    $form->bbcode('[notice] ' . __('Структура таблиц базы данных будет изменена.'));
    $form->button(__('Выполнить запросы'), 'load');
    $form->display();
} else {
    $listing->display(__('Все таблицы находятся в актуальном состоянии'));
}

$doc->ret(__('Админка'), './');
