
{section name=i loop=$err}
<div class="err">{$err[i].text|output_text}{if $err[i].help} [<a title="Помощь" href="/faq.php?info={$err[i].help}&amp;return={$return|urlencode}">?</a>]{/if}</div>
{/section}
{section name=i loop=$msg}
<div class="msg">{$msg[i].text|output_text}{if $msg[i].help} [<a title="Помощь" href="/faq.php?info={$msg[i].help}&amp;return={$return|urlencode}">?</a>]{/if}</div>
{/section}


<div class="user_aut">{if $user->id}
<a href="/menu.user.php">{$user->login}</a>
{if $user->mail_new_count}<br /><a href='/my.mail.php?only_unreaded'>{$lang->getString("Почта")} +{$user->mail_new_count}</a>{/if}
{if $user->friend_new_count}<br /><a href='/my.friends.php'>{$lang->getString("Друзья")} +{$user->friend_new_count}</a>{/if}

{else}
<a href="/login.php?return={$URL}">{$lang->getString("Авторизация")}</a>
<a href="/reg.php?return={$URL}">{$lang->getString("Регистрация")}</a>
{/if}
</div>
