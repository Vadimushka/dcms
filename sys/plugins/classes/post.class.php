<?php

class post {

    var
            $id = false,
            $title,
            $post,
            $actions = array(),
            $url = false,
            $icon = false,
            $checkbox = false,
            $new = false,
            $hide = false,
            $bottom = '';

    function __construct($title = '', $post = '') {
        $this->title = $title;
        $this->post = $post;
    }

    function icon($name) {
        global $doc;
        if ($icon_src = $doc->getIconPath($name)) {
            $this->icon = array('size' => 'small', 'src' => $icon_src);
        }
    }

    function image($icon_src, $small = false) {
        $this->icon = array('size' => $small ? 'small' : 'big', 'src' => $icon_src);
    }

    function actionAdd($name, $url) {
        $this->actions[] = array($name, $url);
    }
    
    function ckeckbox($name, $checked = false){
        $this->checkbox = array($name, $checked);
    }

    function toListing() {
        $return = array();
        $return['id'] = $this->id;
        $return['title'] = $this->title;
        $return['post'] = $this->post;
        $return['act'] = $this->actions;
        $return['url'] = $this->url;
        $return['icon'] = $this->icon;
        $return['checkbox'] = $this->checkbox;
        $return['new'] = $this->new;
        $return['hide'] = $this->hide;
        $return['edit'] = $this->bottom;
        return $return;
    }

}

?>
