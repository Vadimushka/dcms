<div class='user'>
    <? if ($user->id) { ?>
        <a id='user_mail' href='/my.mail.php?only_unreaded' class='<?= $user->mail_new_count ? '' : 'hide' ?>'><?= __("Почта") ?> +<span><?= $user->mail_new_count ?></span></a><!--
        --><a id='user_friend' href='/my.friends.php' class='<?= $user->friend_new_count ? '' : 'hide' ?>'><?= __("Друзья") ?> +<span><?= $user->friend_new_count ?></span></a><!--
        --><a id='user_login' href="/menu.user.php"><?= $user->login ?></a>
    <? } else { ?>
        <a href="/login.php?return=<?= URL ?>"><?= __("Авторизация") ?></a><a href="/reg.php?return=<?= URL ?>"><?= __("Регистрация") ?></a>    
    <? } ?>
</div>

<?= $this->section($err, '<div class="err">{text}</div>'); ?>
<?= $this->section($msg, '<div class="msg">{text}</div>'); ?>

<? if ($user->id) { ?>
    <script type="text/javascript">    
        var USER = {
            id: <?= $user->id ?>,
            mail_new_count: <?= $user->mail_new_count ?>,
            friend_new_count: <?= $user->friend_new_count ?>
        };
        DCMS_USER_UPDATE.update();  // запускаем периодический запрос данных пользователя
        // новые данные можно получать, подписавшись на событие user_update: DCMS.Event.on('user_update', user_update);
    </script>
<?
}?>