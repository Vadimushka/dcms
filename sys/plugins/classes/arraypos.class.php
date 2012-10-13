<?php
// мега велосипед для работы с позициями элементов в массиве.

abstract class arraypos {

    // получение позиции элемента в массиве
    static function getPosition($array, $key) {
        $i = 1;
        foreach ($array as $key2 => $value) {
            if ($key2 == $key) {
                return $i;
            }
            $i++;
        }
        return false;
    }

    // установка позиции элемента
    static function setPosition(&$array, $key, $step_to = 1) {
        
        if (!isset($array[$key])){
            return false;
        }
        
        
        $step_to--;
        $step_of = self::getPosition($array, $key) - 1;
        if (!isset($array[$key])) {
            return false;
        }
        $tmp_array = array();

        foreach ($array as $key2 => $value) {
            $tmp_array[] = array('key' => $key2, 'value' => $value);
        }

        if ($step_to == $step_of) {
            // если позиция соответствует
            return true;
        }

        
        $move = $tmp_array[$step_of];
        if (isset($tmp_array[$step_to])) {
            // опускаем элементы для освобождения требуемой позиции
            $i = count($tmp_array) - 1;
            for ($i; $i >= $step_to; $i--) {
                $tmp_array[$i + 1] = $tmp_array[$i];
            }

            if ($step_of > $step_to) {
                // если у элемента исходная позиция ниже требуемой, значит он был опущен с остальными элементами для освобождения новой позиции
                $step_of++;
            } else {
                $step_to++;
            }
        }
        unset($tmp_array[$step_of]);
        $tmp_array[$step_to] = $move;


        ksort($tmp_array);
        reset($tmp_array);
        $array = array();
        foreach ($tmp_array as $value) {
            $array[$value['key']] = $value['value'];
        }

        return true;
    }

    // перемещение ключа $key массива $array на $step шагов
    static function move(&$array, $key, $step = 1) {
        $pos = self::getPosition($array, $key);
        return self::setPosition($array, $key, $pos + $step);
    }

}

?>
