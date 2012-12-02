<?php

class form extends ui {

    public function __construct($url = '', $post = true) {
        parent::__construct();
        $this->_tpl_file = 'input.form.tpl';

        $this->_data['el'] = array();

        $this->set_url($url);
        $this->set_method($post ? 'post' : 'get');
    }

    function html($html, $br = false){
        $this->_data['el'][] = array(
            'type' => 'text',
            'br' => (bool) $br,
            'value' => $html
        );
    }    
    
    function bbcode($bbcode, $br = true) {
        $this->html(text::output_text($bbcode), $br);
    }

    function checkbox($name, $title, $checked = false, $br = true, $value = '1') {
        $this->_data['el'][] = array(
            'type' => 'checkbox',
            'br' => (bool) $br,
            'info' => array(
                'name' => text::for_value($name),
                'checked' => (bool) $checked,
                'value' => text::for_value($value),
                'text' => text::for_value($title)
            )
        );
    }

    function select($name, $title, $options, $br = true) {
        $this->_data['el'][] = array(
            'type' => 'select',
            'title' => text::for_value($title),
            'br' => (bool) $br,
            'info' => array(
                'name' => text::for_value($name),
                'options' => (array) $options
            )
        );
    }

    function button($text, $name = '', $br = true) {
        $this->input($name, '', $text, 'submit', $br);
    }

    function file($name, $title, $br = true) {
        $this->input($name, $title, false, 'file', $br);
    }

    function captcha($br = true) {
        $this->_data['el'][] = array('type' => 'captcha', 'br' => $br, 'session' => captcha::gen());
    }

    function password($name, $title, $value = '', $br = true, $size = false) {
        $this->input($name, $title, $value, 'password', $br, $size);
    }

    function text($name, $title, $value = '', $br = true, $size = false, $disabled = false) {
        $this->input($name, $title, $value, 'text', $br, $size, $disabled);
    }

    function hidden($name, $value) {
        $this->input($name, '', $value, 'hidden', false);
    }

    function textarea($name, $title, $value = '', $br = true, $disabled = false) {
        $this->input($name, $title, $value, 'textarea', $br, false, $disabled);
    }

    function input($name, $title, $value = '', $type = 'text', $br = true, $size = false, $disabled = false, $maxlength = false) {
        if (!in_array($type, array('text', 'input_text', 'password', 'hidden', 'textarea', 'submit', 'file')))
            return false;

        $input = array();

        if ($type == 'file')
            $this->set_is_files();

        if ($type == 'text')
            $type = 'input_text'; // так уж изначально было задумано. Избавляться будем постепенно

        $input['type'] = $type;
        $input['title'] = text::for_value($title);
        $input['br'] = (bool) $br;

        $info = array();
        $info['name'] = text::for_value($name);
        $info['value'] = $value;

        $info['disabled'] = (bool) $disabled;

        if ($size)
            $info['size'] = (int) $size;
        if ($maxlength)
            $info['maxlength'] = (int) $maxlength;

        $input['info'] = $info;
        $this->_data['el'][] = $input;
        return true;
    }

    function set_method($method) {
        if (in_array($method, array('get', 'post')))
            $this->_data['method'] = $method;
    }

    function set_url($url) {
        $this->_data['action'] = $url;
    }

    function set_is_files() {
        $this->_data['method'] = 'post';
        $this->_data['files'] = true;
    }

}

?>
