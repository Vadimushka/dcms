<?php

include_once '../sys/inc/start.php';
dpanel::check_access();
$doc = new document(groups::max());
$doc->title = __('Сохранение таблиц');

$dbStructure = new \Dcms\Helpers\DbStructure\DbStructure();
$dbStructure->loadFromBase(Db::me());

$tables = [];
foreach ($dbStructure->Tables as $table) {
    $tables[] = $table->Properties->Name;
}

if (!empty($_POST)) {
    foreach ($_POST as $table => $val) {
        if (!$val)
            continue;
        if (in_array($table, $tables)) {
            if (function_exists('set_time_limit'))
                set_time_limit(600);

            if (!empty($_POST['create'])) {
                $tableStructure = new \Dcms\Helpers\DbStructure\DbStructureTable();
                $tableStructure->loadFromBase(Db::me(), $table);
                $tableStructure->saveToJsonFile(H . '/sys/preinstall/table.' . $table . '.structure.json');
            }
            if (!empty($_POST['data'])) {
                $tableRows = new \Dcms\Helpers\DbStructure\DbStructureRows();
                $tableRows->loadFromBase(Db::me(), $table);
//                $tableRows->saveToJsonFile(H . '/sys/preinstall/table.' . $table . '.data.json');
                $tableRows->saveToSQLReplaceFile(H . '/sys/preinstall/table.' . $table . '.data.sql', Db::me(), $table);
            }
        }
    }

    if (!empty($_POST['create'])) {
        $doc->msg(__("Структура таблиц успешно сохранена"));
    }
    if (!empty($_POST['data'])) {
        $doc->msg(__("Содержимое таблиц успешно сохранено"));
    }

    if (@copy(H . '/sys/ini/settings.ini', H . '/sys/preinstall/settings.ini')) {
        $doc->msg(__("Предустановочные параметры успешно сохранены"));
    }
}

$listing = new listing();
foreach ($tables as $table) {
    if ($table{0} == '~') {
        continue;
    }
    $ch = $listing->checkbox();
    $ch->name = $table;
    $ch->title = $table;
    $ch->checked = true;
}

$form = new form('?' . passgen());
$form->html($listing->fetch());
$form->bbcode('[notice] ' . __('Структура и данные таблиц сохранятся в папке sys/preinstall и в дальнейшем могут быть использованы для установки движка с существующими данными'));
$form->button(__('Структура'), 'create', false);
$form->button(__('Данные'), 'data', false);
$form->display();

$doc->ret(__('Админка'), './');