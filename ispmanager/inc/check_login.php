<?php

if (!$isp->isAutorized()) {
    $doc->msg('Необходима авторизация');
    $listing = new listing();
    $post = $listing->post();
    $post->icon('authentication');
    $post->url = 'auth.php';
    $post->title = 'Авторизация';
    $listing->display();
    exit;
}
?>
