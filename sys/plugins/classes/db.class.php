<?php

class db extends PDO{
    function __construct($host, $basename, $username, $passwd, $type = 'mysql') {
        
        switch($type){
            case 'mysql':$dsn = "mysql:host=$host;dbname=$basename";break;
        }
        
        
        parent::__construct($dsn, $username, $passwd);
        
    }
}

?>
