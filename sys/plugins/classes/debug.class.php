<?php

abstract class debug {

    static protected function l($item = '') {
        static $list = array();
        if ($item)
            $list[] = $item;
        return $list;
    }

    static function step($comment = '') {
        if (!isset($_GET['debug']))
            return false;

        static $laststep = null;
        if ($laststep === null)
            $laststep = microtime(true);
        
        
        
        $this_time = microtime(true);
        $delta = round($this_time - $laststep, 3);
        
        $laststep = microtime(true);
        self::l('<!-- ' . number_format($delta, 3, '.', '') . ' ' . $comment . ' -->');
    }

    static function fetch() {
        if (!isset($_GET['debug']))
            return false;

        return implode("\r\n", self::l());
    }
    
    static function display() {
        echo self::fetch();
    }
    

}

?>
