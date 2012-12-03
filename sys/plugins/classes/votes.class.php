<?php

class votes {

    protected $_list = array();
    public $description;
    protected $_count_max = 0;

    public function __construct($description = '') {
        $this->description = $description;
    }

    public function vote($name, $count, $url) {
        $this->_count_max = max($this->_count_max, $count);
        $this->_list[] = array('name' => $name, 'count' => $count, 'url' => $url);
    }

    public function display($is_add = false) {
        if (!$this->_list) {
            return false;
        }


        $vote_tpl = new design();
        $vote_tpl->assign('name', $this->description, 1);
        $votes = array();
        foreach ($this->_list as $item) {
            $votes[] = array('name' => text::filter($item['name'], 1), 'url' => $item['url'], 'count' => $item['count'], 'pc' => @round($item['count'] / $this->_count_max * 100));
        }
        $vote_tpl->assign('votes', $votes);
        $vote_tpl->assign('is_add', $is_add);
        $vote_tpl->display('votes.tpl');
    }

}

?>
