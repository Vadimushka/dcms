<?php

class html_parser {

    protected $_inputs = array();
    protected $_selects = array();
    protected $_textareas = array();

    function __construct($html = false) {
        if ($html)
            $this->parse($html);
    }

    function parse($html) {
        echo '<!--' . $html . '-->';
        $this->_inputs = $this->_parseInputs($html);
        $this->_selects = $this->_parseSelects($html);
        $this->_textareas = $this->_parseTextareas($html);
    }

    /**
     * Получаем значение поля по-умолчанию
     * @param type $name
     * @return type
     */
    function getValue($name) {
        if ($input = $this->getInputByName($name)) {
            return @$input['value'];
        }
        if ($textarea = $this->getTextareaByName($name)) {
            return $textarea['value'];
        }


        return $this->getSelectValue($name);
    }

    /**
     * проверка, присутствует ли селект
     * @param type $name
     * @return boolean
     */
    function hasSelect($name) {
        if (!$select = $this->getSelectByName($name))
            return false;
        return true;
    }

    /**
     * проверка, присутствует ли опция у селекта
     * @param type $name
     * @param type $value
     * @return boolean
     */
    function hasSelectOption($name, $value) {
        if (!$select = $this->getSelectByName($name))
            return false;

        foreach ($select['options'] as $option) {
            if (!empty($option['value']) && $option['value'] == $value || $option['text'] == $value)
                return true;
        }

        return false;
    }

    function getSelectOptions($name) {
        if (!$select = $this->getSelectByName($name))
            return array();
        $options = array();
        foreach ($select['options'] AS $option) {
            $value = isset($option['value']) ? $option['value'] : $option['text'];
            $options[] = array($value, $option['text'], !empty($option['selected']));
        }
        return $options;
    }

    function getSelectValue($name) {

        if (!$select = $this->getSelectByName($name))
            return false;

        foreach ($select['options'] as $option) {
            if (!empty($option['selected']))
                return isset($option['value']) ? $option['value'] : $option['text'];
        }

        return false;
    }

    function getTextareaByName($name) {
        foreach ($this->_textareas AS $textarea) {
            if (isset($textarea['name']) && $textarea['name'] == $name)
                return $textarea;
        }
        return false;
    }

    function getInputByName($name) {
        foreach ($this->_inputs AS $input) {
            if (isset($input['name']) && $input['name'] == $name)
                return $input;
        }
        return false;
    }

    function getSelectByName($name) {
        foreach ($this->_selects AS $select) {
            if (isset($select['name']) && $select['name'] == $name)
                return $select;
        }
        return false;
    }

    protected function _parseSelects($html) {
        $selects = array();
        preg_match_all('/\<select (.*?)\>(.*?)\<\/select\>/sui', $html, $matches, PREG_SET_ORDER);
        for ($i = 0; $i < count($matches); $i++) {
            $select = array();
            $options = array();
            preg_match_all('/\<option (.*?)\>(.*?)\<\/option>/sui', $matches[$i][2], $omatches, PREG_SET_ORDER);
            for ($o = 0; $o < count($omatches); $o++) {
                $options_data = explode(' ', $omatches[$o][1]);
                $option = array();
                for ($od = 0; $od < count($options_data); $od++) {
                    if (preg_match('/([^=]+)(="(.+)")?/', $options_data[$od], $m)) {
                        $option[$m[1]] = !isset($m[3]) ? $m[1] : $m[3];
                    }
                }
                $option['text'] = $omatches[$o][2];
                $options[] = $option;
            }


            preg_match_all('/(^| )([^=]+)(="(.*?)")/sui', $matches[$i][1], $m, PREG_SET_ORDER);
            for ($im = 0; $im < count($m); $im++) {
                $select[$m[$im][2]] = $m[$im][4];
            }


            $select['options'] = $options;
            $selects[] = $select;
        }
        return $selects;
    }

    protected function _parseInputs($html) {
        $inputs = array();
        preg_match_all('/\<input (.*?)\>/sui', $html, $matches, PREG_SET_ORDER);
        for ($i = 0; $i < count($matches); $i++) {
            $input = array();
            preg_match_all('/(^| )([^=]+)(="(.*?)")/sui', $matches[$i][1], $m, PREG_SET_ORDER);
            for ($im = 0; $im < count($m); $im++) {
                $input[$m[$im][2]] = $m[$im][4];
            }
            $inputs[] = $input;
        }
        return $inputs;
    }

    protected function _parseTextareas($html) {
        $textareas = array();
        preg_match_all('/\<textarea (.*?)\>(.*?)\<\/textarea>/sui', $html, $matches, PREG_SET_ORDER);
        for ($i = 0; $i < count($matches); $i++) {
            $textarea = array();
            preg_match_all('/(^| )([^=]+)(="(.*?)")/', $matches[$i][1], $m, PREG_SET_ORDER);
            for ($im = 0; $im < count($m); $im++) {
                $textarea[$m[$im][2]] = $m[$im][4];
            }
            $textarea['value'] = $matches[$i][2];
            $textareas[] = $textarea;
        }
        return $textareas;
    }

}

?>
