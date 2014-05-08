<?
if ($user->id) {
    ?>
    <span class="gradient_blue"><?= $user->login ?></span>
    <a class="gradient_grey" href="/menu.user.php"><?= __("Личное меню") ?></a>
    <a class="gradient_grey" data-ng-show="dcms.user.friend_new_count!=0" href='/my.friends.php'><?= __("Друзья") ?> +{{dcms.user.friend_new_count}}</a>
    <a class="gradient_grey" data-ng-show="dcms.user.mail_new_count!=0" href='/my.mail.php?only_unreaded'><?= __("Почта") ?> +{{dcms.user.mail_new_count}}</a>
<?
} else {
    ?>
    <a class="gradient_grey" href="/login.php?return=<?= URL ?>"><?= __("Авторизация") ?></a>
    <a class="gradient_grey" href="/reg.php?return=<?= URL ?>"><?= __("Регистрация") ?></a>
<?
}