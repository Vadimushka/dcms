<?php

include_once '../sys/inc/start.php';
$doc = new document();
$doc->title = 'Панель управления';
$isp = new ispmanager();
include_once 'inc/check_login.php';

$listing = new listing();

$post = $listing->post();
$post -> icon('domain');
$post->url = 'domain.php';
$post->title = 'Домены (DNS)';

$post = $listing->post();
$post -> icon('www');
$post->url = 'wwwdomain.php';
$post->title = 'WWW - домены';

$post = $listing->post();
$post -> icon('ftp');
$post->url = 'ftp.php';
$post->title = 'FTP - аккаунты';

$post = $listing->post();
$post -> icon('hdd');
$post->url = 'diskusage.php';
$post->title = 'Использование диска';

/*
$post = $listing->post();
$post -> icon('resources');
$post->url = 'usersystemresources.php';
$post->title = 'Системные ресурсы';
*/

$post = $listing->post();
$post -> icon('exit');
$post->url = 'logout.php';
$post->title = 'Выход';

$listing->display();
?>
