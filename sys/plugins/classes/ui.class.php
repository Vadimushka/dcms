<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ui
 *
 * @author DESURE
 */
class ui {

    protected $_tpl_file;
    protected $_data = array();

    public function __construct() {
        $this->_data['id'] = $this->_getNewId();
    }
    
    /**
     * Возвращает уникальный идентификатор класса на странице
     * @staticvar array $id
     * @return type
     */
    protected function _getNewId() {
        static $id = array();
        $class = get_class($this);
        return $class . '_' . @++$id[$class];
    }

    public function fetch() {
        $tpl = new design();
        $tpl->assign($this->_data);
        return $tpl->fetch($this->_tpl_file);
    }

    public function display() {
        echo $this->fetch();
    }

}

?>
