<?php
function count_line_files($name_file){
    $path = 'files/'.$name_file.'.txt';
    if(file_exists($path)){
        $file_handle = fopen($path, 'r');
        $line_count = fread($file_handle, 100000);
        $count = strlen($line_count);
    }else{
        $count = $doc->err('Ошибка, проверьте существование файла на сервере');
    }
    return $count; 
}
?>