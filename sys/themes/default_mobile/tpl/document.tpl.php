<?php
/**
 * @var $this document
 */
?><!DOCTYPE html>
<html>
<head>
    <title><?= $title ?></title>
    <link rel="shortcut icon" href="/favicon.ico"/>
    <link rel="stylesheet" href="/sys/themes/.common/system.css" type="text/css"/>
    <link rel="stylesheet" href="/sys/themes/.common/icons.css" type="text/css"/>
    <link rel="stylesheet" href="/sys/themes/.common/theme_light.css" type="text/css"/>
    <link rel="stylesheet" href="<?= $path ?>/style.css?6" type="text/css"/>
    <meta name="viewport" content="minimum-scale=1.0,initial-scale=1.0,maximum-scale=1.0,user-scalable=no"/>
    <meta name="generator" content="DCMS <?= $dcms->version ?>"/>
    <? if ($description) { ?>
        <meta name="description" content="<?= $description ?>" /><? } ?>
    <? if ($keywords) { ?>
        <meta name="keywords" content="<?= $keywords ?>" /><? } ?>
    <script>
        var translate = {
            'friends': "<?=__("Друзья")?>",
            'mail': "<?=__("Почта")?>",
            'user_menu': "<?= __("Личное меню") ?>",
            'auth': "<?= __("Авторизация") ?>",
            'reg': "<?= __("Регистрация") ?>",
            rating_down_message: '<?=__('Подтвердите понижение рейтинга сообщения.').($dcms->forum_rating_down_balls?"\\n".__('Будет списано баллов: %s',$dcms->forum_rating_down_balls):'')?>'
        };

        var user = {
            'id': "<?=$user->id?>",
            'group': "<?=$user->group?>",
            'friend_new_count': "<?=$user->friend_new_count?>",
            'mail_new_count': "<?=$user->mail_new_count?>",
            'login': "<?=$user->login?>"
        };

        var URL = "<?=URL?>";
    </script>
    <script src="/sys/themes/.common/jquery-2.1.1.min.js"></script>
    <script src="/sys/themes/.common/dcmsApi.js"></script>
    <script src="<?= $path ?>/js.js?6"></script>
</head>
<body class="theme_light">
<audio id="audio_notify" preload="auto">
    <source src="/sys/themes/.common/notify.mp3" />
    <source src="/sys/themes/.common/notify.ogg" />
</audio>
<div id="container_content">
    <h1 id='title' class="gradient_blue">
        <span id="icon_menu"></span>
        <span><?= $title ?></span>
    </h1>
    <? $this->displaySection('after_title') ?>
    <div id="tabs">
        <?= $this->section($tabs, '<a class="gradient_grey border tab sel{selected}" href="{url}">{name}</a>', true); ?>
    </div>
    <? $this->displaySection('before_content') ?>
    <div id="content">
        <div id="messages">
            <?= $this->section($err, '<div class="gradient_red border radius">{text}</div>'); ?>
            <?= $this->section($msg, '<div class="gradient_green border radius">{text}</div>'); ?>
        </div>
        <?php $this->displaySection('content') ?>
    </div>
    <? $this->displaySection('after_content') ?>
    <? $this->display('inc.foot.tpl') ?>
    <div id="foot">
        <?= __("Время генерации страницы: %s сек", $document_generation_time) ?><br/>
        <?= $copyright ?>
    </div>
</div>
<div id="container_overflow"></div>
<div id="container_menu">
    <span id="user" class="gradient_blue"><?= $user->nick ?></span>
    <a id="login" class="gradient_grey" href="/login.php?return=<?= URL ?>"></a>
    <a id="reg" class="gradient_grey" href="/reg.php?return=<?= URL ?>"></a>
    <? $this->displaySection('menu') ?>
</div>
</body>
</html>