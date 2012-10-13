<?php

abstract class keyvalue {

    public static function read($path) {
        $array = array();
        if (!$file = @file_get_contents($path)) {
            return false;
        }
        $m = array();
        preg_match_all('/^ \s* (.+) \s* = \s* "(.+)" \s* $/exm', $file, $m, PREG_SET_ORDER);

        for ($i = 0; $i < count($m); $i++) {
            $array[trim($m[$i][1])] = $m[$i][2];
        }
        return $array;
    }

    public static function save($path, $array) {
        $tmp_file = TMP . '/tmp.' . passgen() . '.ini';
        $content = array();
        $content[] = ";Saved by keyvalue.class";
        foreach ($array as $key => $value) {
            $content[] = $key . ' = "' . $value . '"';
        }


        // сохраняем во временный файл
        if (!@file_put_contents($tmp_file, implode("\r\n", $content))) {
            return false;
        }
        @chmod($tmp_file, filesystem::getChmodToWrite());

        if (IS_WINDOWS) {
            // в винде файл перед заменой нужно удалить
            if (@file_exists($path) && !@unlink($path)) {
                return false;
            }
        }
        // переименовываем временный файл в нужный нам
        if (!@rename($tmp_file, $path)) {
            return false;
        }


        return true;
    }

}

?>
