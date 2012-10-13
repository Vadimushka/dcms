<?php

abstract class dpanel {

    static function check_access() {
        if (self::is_access()) {
            self::access();
        } else {
            header("Location: /dpanel/login.php?return=" . URL . '&' . SID);
            exit;
        }
    }

    static function is_access() {        
        return cache_dpanel_access::get(self::key());
    }

    static function access() {
        
        cache_dpanel_access::set(self::key(), true, 3600);
    }

    static function access_delete() {        
        cache_dpanel_access::set(self::key(), false, 1);
    }

    static function key(){
        global $dcms, $user;
        return 'dpanel.access.'.$user->id . '.' . (string)$dcms->ip_long . '.' . (string)$dcms->browser;
    }
    
}

?>
