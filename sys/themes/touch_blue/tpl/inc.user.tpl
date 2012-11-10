<div class='user'>
    {if $user->id}

<a id='user_mail' href='/my.mail.php?only_unreaded' class='{if !$user->mail_new_count}hide{/if}'>{$lang->getString("Почта")} +<span>{$user->mail_new_count}</span></a><!--
--><a id='user_friends' href='/my.friends.php' class='{if !$user->friend_new_count}hide{/if}'>{$lang->getString("Друзья")} +<span>{$user->friend_new_count}</span></a><!--
--><a id='user_login' href="/menu.user.php">{$user->login}</a>
{else}
    <a href="/login.php?return={$URL}">{$lang->getString("Авторизация")}</a><a href="/reg.php?return={$URL}">{$lang->getString("Регистрация")}</a>    
{/if}
</div>



{section name=i loop=$err}
    <div class="err">{$err[i].text|output_text}{if $err[i].help} [<a title="{$lang->getString("Помощь")}" href="/faq.php?info={$err[i].help}&amp;return={$return|urlencode}">?</a>]{/if}</div>
{/section}

{section name=i loop=$msg}
    <div class="msg">{$msg[i].text|output_text}{if $msg[i].help} [<a title="{$lang->getString("Помощь")}" href="/faq.php?info={$msg[i].help}&amp;return={$return|urlencode}">?</a>]{/if}</div>
{/section}

{if $user->id}
<script type="text/javascript">    
    var USER = {
        id: {$user->id},
        mail_new_count: {$user->mail_new_count},
        friend_new_count: {$user->friend_new_count}
    }; 
    
    DCMS_USER_UPDATE.update();  // запускаем периодический запрос данных пользователя
    // новые данные можно получать, подписавшись на событие user_update: DCMS.Event.on('user_update', user_update);
</script>
{/if}