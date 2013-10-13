<?php
// реклама SAPE только для dcms.su
// убирать не обязательно, так как нигде больше она отображаться не будет
if (preg_match('/(^|\.)dcms\.su\.?$/', $_SERVER['HTTP_HOST'])) {
    if (!defined('_SAPE_USER'))
        define('_SAPE_USER', 'c55bf3fc219b9610c2b8abde2d8ed171');

    require_once(H . '/' . _SAPE_USER . '/sape.php');
    echo "<!--check code-->";
    $sape = new SAPE_client(array(
        'force_show_code' => true,
        'charset' => 'UTF-8'
    ));
    echo $sape->return_links();
    echo "<!--check code-->";
}