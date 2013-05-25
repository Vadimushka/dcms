<?php

abstract class smiles {

    static function get_ini() {
        static $ini = false;
        if ($ini === false) {
            $ini = (array) ini::read(H . '/sys/ini/smiles.ini');
        }
        return $ini;
    }

    /**
     * Обработка смайлов во входящем сообщении
     * @param type $str
     * @return type
     */
    static function input($str) {
        $smiles = self::get_ini();
        $str = preg_replace('#([\.:\*])(' . implode('|', array_keys($smiles)) . ')\1#uim', '[smile]\2[/smile]', $str);
        return $str;
    }

    static function bbcode($smile) {
        $smiles = self::get_ini();
        if (empty($smiles[$smile])) {
            return false;
        }
        return '<img src="/sys/images/smiles/' . $smiles[$smile] . '.gif" alt="' . $smile . '" />';
    }
}