<div class="user">
    
    {section name=i loop=$actions}<a href="{$actions[i].1}">{$actions[i].0}</a>
    {/section}    
    
    {if $user->id}
        <script>message_file_path = '{$path}/sound.swf';id_user = {$user->id};count_mail = {$user->mail_new_count};count_friends = {$user->friend_new_count};</script>
        <a id='friends' style='font-weight: bold; display: none' href='/my.friends.php'>{$lang->getString("Друзья")}</a>
        <a id='mail' style='font-weight: bold; display: none' href='/my.mail.php?only_unreaded'>{$lang->getString("Почта")}</a>
        <a id='menu_user' style='font-weight: bold;' href="/menu.user.php">{$user->login}</a>
    {else}
        <script>id_user = 0;</script>
        <a href="/login.php?return={$URL}">{$lang->getString("Авторизация")}</a>
        <a href="/reg.php?return={$URL}">{$lang->getString("Регистрация")}</a>
    {/if}
</div>