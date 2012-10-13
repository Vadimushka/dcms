<?php
class tables {
    var $tables = array();

    function __construct()
    {
        $tab = mysql_query('SHOW TABLES');
        while ($table = mysql_fetch_array($tab)) {
            $this->tables[] = $table[0];
        }
    }
    // получение sql запроса на создание таблицы
    function get_create($table, $auto_increment = true)
    {
        $sql = "/* Структура таблицы `$table` */\r\n";
        $row = mysql_fetch_row(mysql_query("SHOW CREATE TABLE `" . my_esc($table) . "`"));
        if (!$auto_increment) {
            $row[1] = preg_replace('#AUTO_INCREMENT\=[0-9]+#ui', '/*\0*/', $row[1]);
        }
        return $sql . $row[1];
    }
    function get_data($table, $c_ins = 2000)
    {
        $sql = '';
        $num_row_all = mysql_result(mysql_query("SELECT COUNT(*) FROM `" . my_esc($table) . "`"), 0);
        $start = 0;

        if ($num_row_all) {
            $sql .= "/* Данные таблицы `$table` */\r\n";
            $table_keys = @implode("`, `", @array_keys(mysql_fetch_assoc(mysql_query("SELECT * FROM `" . my_esc($table) . "` LIMIT 1"))));
            while ($start < $num_row_all) {
                $res = mysql_query("SELECT * FROM `$table` LIMIT $start, $c_ins");

                if ($num_row_all > $c_ins)$sql .= "/* блок записей $start - " . ($start + $c_ins) . " */\r\n";

                $sql .= "INSERT INTO `" . my_esc($table) . "` (`$table_keys`) VALUES \r\n";
                $num_row = mysql_num_rows($res);
                $counter = 0;
                while (($row = @mysql_fetch_assoc($res))) {
                    $values = @array_values($row);

                    foreach($values as $k => $v) {
                        $values[$k] = "'" . preg_replace("#(\n|\r)+#", '\n', my_esc($v)) . "'";
                    }
                    $values_string = @implode(', ', $values);
                    $counter++;
                    $sql .= "($values_string)" . ($counter == $num_row?";\r\n":", \r\n");
                }
                $start = $start + $c_ins;
            }
        } else $sql .= "/* Таблица `$table` пуста */\r\n";

        return $sql;
    }
    function save_create($path, $table, $ai = 0)
    {
        return @file_put_contents($path, $this->get_create($table, $ai));
    }
    function save_data($path, $table, $c_ins = 2000)
    {
        return @file_put_contents($path, $this->get_data($table, $c_ins));
    }
}

?>
