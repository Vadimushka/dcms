<?
if ($user->id) {
    ?>
    <span class="gradient_blue"><?= $user->login ?></span>
    <a class="gradient_grey" href="/menu.user.php"><?= __("Личное меню") ?></a>
    <a class="gradient_grey" href='/my.friends.php'><?= __("Друзья") ?>{{dcms.getFriends()}}</a>
    <a class="gradient_grey" href='{{dcms.getMailHref()}}'><?= __("Почта") ?>{{dcms.getMails()}}</a>
<?
} else {
    ?>
    <a class="gradient_grey" href="/login.php?return=<?= URL ?>"><?= __("Авторизация") ?></a>
    <a class="gradient_grey" href="/reg.php?return=<?= URL ?>"><?= __("Регистрация") ?></a>
<?
}