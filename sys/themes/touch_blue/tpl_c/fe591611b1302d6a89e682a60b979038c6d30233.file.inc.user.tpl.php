<?php /* Smarty version Smarty-3.0.6, created on 2012-11-09 21:58:06
         compiled from "E:/DESURE/Dropbox/domains/dcms7dev/sys/themes/touch_blue/tpl/inc.user.tpl" */ ?>
<?php /*%%SmartyHeaderCode:3378509d442ed39d66-28781857%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'fe591611b1302d6a89e682a60b979038c6d30233' => 
    array (
      0 => 'E:/DESURE/Dropbox/domains/dcms7dev/sys/themes/touch_blue/tpl/inc.user.tpl',
      1 => 1352483508,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '3378509d442ed39d66-28781857',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<div class='user'>
    <?php if ($_smarty_tpl->getVariable('user')->value->id){?>

<a id='user_mail' href='/my.mail.php?only_unreaded' class='<?php if (!$_smarty_tpl->getVariable('user')->value->mail_new_count){?>hide<?php }?>'><?php echo $_smarty_tpl->getVariable('lang')->value->getString("Почта");?>
 +<span><?php echo $_smarty_tpl->getVariable('user')->value->mail_new_count;?>
</span></a><!--
--><a id='user_friends' href='/my.friends.php' class='<?php if (!$_smarty_tpl->getVariable('user')->value->friend_new_count){?>hide<?php }?>'><?php echo $_smarty_tpl->getVariable('lang')->value->getString("Друзья");?>
 +<span><?php echo $_smarty_tpl->getVariable('user')->value->friend_new_count;?>
</span></a><!--
--><a id='user_login' href="/menu.user.php"><?php echo $_smarty_tpl->getVariable('user')->value->login;?>
</a>
<?php }else{ ?>
    <a href="/login.php?return=<?php echo $_smarty_tpl->getVariable('URL')->value;?>
"><?php echo $_smarty_tpl->getVariable('lang')->value->getString("Авторизация");?>
</a><a href="/reg.php?return=<?php echo $_smarty_tpl->getVariable('URL')->value;?>
"><?php echo $_smarty_tpl->getVariable('lang')->value->getString("Регистрация");?>
</a>    
<?php }?>
</div>



<?php unset($_smarty_tpl->tpl_vars['smarty']->value['section']['i']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['name'] = 'i';
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop'] = is_array($_loop=$_smarty_tpl->getVariable('err')->value) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['show'] = true;
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['max'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'] = 1;
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['start'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'] > 0 ? 0 : $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop']-1;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['show']) {
    $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop'];
    if ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total'] == 0)
        $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['show'] = false;
} else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total'] = 0;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['show']):

            for ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'] = 1;
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'] <= $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total'];
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index'] += $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'], $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration']++):
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['rownum'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index_prev'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index_next'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['first']      = ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'] == 1);
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['last']       = ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'] == $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total']);
?>
    <div class="err"><?php echo output_text($_smarty_tpl->getVariable('err')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['text']);?>
<?php if ($_smarty_tpl->getVariable('err')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['help']){?> [<a title="<?php echo $_smarty_tpl->getVariable('lang')->value->getString("Помощь");?>
" href="/faq.php?info=<?php echo $_smarty_tpl->getVariable('err')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['help'];?>
&amp;return=<?php echo urlencode($_smarty_tpl->getVariable('return')->value);?>
">?</a>]<?php }?></div>
<?php endfor; endif; ?>

<?php unset($_smarty_tpl->tpl_vars['smarty']->value['section']['i']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['name'] = 'i';
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop'] = is_array($_loop=$_smarty_tpl->getVariable('msg')->value) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['show'] = true;
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['max'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'] = 1;
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['start'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'] > 0 ? 0 : $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop']-1;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['show']) {
    $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop'];
    if ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total'] == 0)
        $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['show'] = false;
} else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total'] = 0;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['show']):

            for ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'] = 1;
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'] <= $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total'];
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index'] += $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'], $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration']++):
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['rownum'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index_prev'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index_next'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['index'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['first']      = ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'] == 1);
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['last']       = ($_smarty_tpl->tpl_vars['smarty']->value['section']['i']['iteration'] == $_smarty_tpl->tpl_vars['smarty']->value['section']['i']['total']);
?>
    <div class="msg"><?php echo output_text($_smarty_tpl->getVariable('msg')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['text']);?>
<?php if ($_smarty_tpl->getVariable('msg')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['help']){?> [<a title="<?php echo $_smarty_tpl->getVariable('lang')->value->getString("Помощь");?>
" href="/faq.php?info=<?php echo $_smarty_tpl->getVariable('msg')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['help'];?>
&amp;return=<?php echo urlencode($_smarty_tpl->getVariable('return')->value);?>
">?</a>]<?php }?></div>
<?php endfor; endif; ?>

<?php if ($_smarty_tpl->getVariable('user')->value->id){?>
<script type="text/javascript">    
    var USER = {
        id: <?php echo $_smarty_tpl->getVariable('user')->value->id;?>
,
        mail_new_count: <?php echo $_smarty_tpl->getVariable('user')->value->mail_new_count;?>
,
        friend_new_count: <?php echo $_smarty_tpl->getVariable('user')->value->friend_new_count;?>

    }; 
    
    DCMS_USER_UPDATE.update();  // запускаем периодический запрос данных пользователя
    // новые данные можно получать, подписавшись на событие user_update: DCMS.Event.on('user_update', user_update);
</script>
<?php }?>