<?php
function skip_files($pathes, $skips, $noskips = false) {
    $return = array();
    $noskips = (array)$noskips;

    foreach ($pathes as $path) { // перебираем все пути
        foreach ($noskips as $noskip) { // файлы, которые не должны быть пропущены
            if ($path === $noskip || strpos($path, $noskip) !== false) {
                $return[] = $path;
                continue(2);
            }
        }



        foreach ($skips as $skip) { // пропускаемые пути
            if ($path === $skip || strpos($path, $skip) !== false) {
                continue(2);
            }
        }
        $return[] = $path;
    }

    return $return;
}



function getBuildList() {
    $builds_files = (array) @glob('hashes/*.ini');
    $builds = array();
    foreach ($builds_files as $path) {
        $builds[] = basename($path, '.ini');
    }
    return $builds;
}




?>
