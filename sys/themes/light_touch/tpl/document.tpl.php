<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html data-ng-app="" xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?= $lang->xml_lang ?>">
<head>
    <title><?= $title ?></title>
    <link rel="shortcut icon" href="/favicon.ico"/>
    <link rel="stylesheet" href="/sys/themes/.common/system.css" type="text/css"/>
    <link rel="stylesheet" href="/sys/themes/.common/theme_light.css" type="text/css"/>
    <script src="/sys/themes/.common/angular.min.js" async="async"></script>
    <link rel="stylesheet" href="<?= $path ?>/style.css" type="text/css"/>
    <meta http-equiv="content-Type" content="application/xhtml+xml; charset=utf-8"/>
    <meta name="viewport" content="minimum-scale=1.0,initial-scale=1.0,maximum-scale=1.0,user-scalable=no"/>
    <meta name="generator" content="DCMS <?= $dcms->version ?>"/>
    <script>
        function vibrate(ms) {
            if (window.navigator.vibrate)
                window.navigator.vibrate(ms);
        }
        function onNewMail() {
            vibrate([100, 100]);
            var audio = document.querySelector("#audio_notify");
            audio.pause();
            audio.loop = false;
            audio.currentTime = 0;
            audio.play();
        }
        function onNewFriend() {
            vibrate(200);
        }

        function DcmsCtrl($scope, $interval, $http) {
            var dcms = $scope.dcms = {
                interval: 7000,
                menu: false,
                user: {
                    friend_new_count: "<?=$user->friend_new_count?>",
                    mail_new_count: "<?=$user->mail_new_count?>",
                    login: "<?=$user->login?>",
                    id: "<?=$user->id?>",
                    group: "<?=$user->group?>"
                },
                onContainerClick: function ($event) {
                    if (dcms.menu) {
                        dcms.menu = false;
                    }
                },
                getFriends: function () {
                    return dcms.user.friend_new_count != "0" ? "+" + dcms.user.friend_new_count : "";
                },
                getMails: function () {
                    return dcms.user.mail_new_count != "0" ? "+" + dcms.user.mail_new_count : "";
                }
            }
            if (dcms.user.group) {
                $interval(function () {
                    $http.get('/ajax/user.json.php?' + Object.keys(dcms.user).join("&amp;"))
                        .success(function ($data) {
                            if (!$data.group || $data.id != dcms.user.id) {
                                // слетела авторизация или изменился пользователь. рефрешим страницу
                                document.location.href = document.location.href;
                                return;
                            }

                            if ($data.mail_new_count > dcms.user.mail_new_count) {
                                onNewMail();
                            }
                            if ($data.friend_new_count > dcms.user.friend_new_count) {
                                onNewFriend();
                            }

                            dcms.user = $data;
                            dcms.interval = 7000;
                        })
                        .error(function () {
                            dcms.interval = 60000; // увеличиваем интервал запросов при ошибке
                        });
                }, dcms.interval);
            }
        }
    </script>
    <? if ($description) { ?>
        <meta name="description" content="<?= $description ?>" /><? } ?>
    <? if ($keywords) { ?>
        <meta name="keywords" content="<?= $keywords ?>" /><? } ?>
    <style>
        .hide {
            display: none !important;
        }
    </style>
</head>
<audio id="audio_notify" preload="auto">
    <source src="/sys/themes/.common/notify.mp3"/>
</audio>
<body class="theme_light" data-ng-controller="DcmsCtrl">
<div id="container_content" data-ng-class="{menu:dcms.menu}">
    <? $this->display('inc.title.tpl') ?>
    <? # $this->display('inc.user.tpl') ?>
    <div id="content">
        <? $this->display('inc.adt.top.tpl') ?>
        <div id="messages">
            <?= $this->section($err, '<div class="gradient_red border radius">{text}</div>'); ?>
            <?= $this->section($msg, '<div class="gradient_green border radius">{text}</div>'); ?>
        </div>
        <?= $content ?>
    </div>
    <? $this->display('inc.foot.tpl') ?>
    <? $this->display('inc.adt.bottom.tpl') ?>
    <div id="foot">
        <?= __("Время генерации страницы: %s сек", $document_generation_time) ?><br/>
        <?= $copyright ?>
    </div>
</div>
<div id="container_overflow" data-ng-class="{menu:dcms.menu}" data-ng-click="dcms.onContainerClick($event)"></div>
<div id="container_menu" data-ng-class="{menu:dcms.menu}">
    <? $this->display('inc.menu.tpl') ?>
</div>
</body>
</html>