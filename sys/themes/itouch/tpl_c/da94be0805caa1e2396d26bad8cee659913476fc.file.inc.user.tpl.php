<?php /* Smarty version Smarty-3.0.6, created on 2012-09-16 21:14:58
         compiled from "/var/www/dcmssu2462/data/www/dcms.su/sys/themes/itouch/tpl/inc.user.tpl" */ ?>
<?php /*%%SmartyHeaderCode:134534626950560912482cf7-84197033%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'da94be0805caa1e2396d26bad8cee659913476fc' => 
    array (
      0 => '/var/www/dcmssu2462/data/www/dcms.su/sys/themes/itouch/tpl/inc.user.tpl',
      1 => 1339315062,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '134534626950560912482cf7-84197033',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
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
    <div data-role="header" data-position="inline" data-theme="e">     
        <h1><?php echo output_text($_smarty_tpl->getVariable('err')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['text']);?>
</h1>
        <?php if ($_smarty_tpl->getVariable('err')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['help']){?>
            <a data-icon="info" data-iconpos="notext" class="ui-btn-right" href="/faq.php?info=<?php echo $_smarty_tpl->getVariable('err')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['help'];?>
&amp;return=<?php echo urlencode($_smarty_tpl->getVariable('return')->value);?>
"><?php echo $_smarty_tpl->getVariable('lang')->value->getString("Помощь");?>
</a>
        <?php }?>
    </div>
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
    <div data-role="header" data-position="inline" data-theme="e">     
        <h1><?php echo output_text($_smarty_tpl->getVariable('msg')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['text']);?>
</h1>
        <?php if ($_smarty_tpl->getVariable('msg')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['help']){?>
            <a data-icon="info" data-iconpos="notext" class="ui-btn-right" href="/faq.php?info=<?php echo $_smarty_tpl->getVariable('msg')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['help'];?>
&amp;return=<?php echo urlencode($_smarty_tpl->getVariable('return')->value);?>
"><?php echo $_smarty_tpl->getVariable('lang')->value->getString("Помощь");?>
</a>
        <?php }?>
    </div>
<?php endfor; endif; ?>



<div data-role="navbar" data-iconpos="left">
    <ul><?php if ($_smarty_tpl->getVariable('user')->value->id){?>
        <li><a data-icon="grid" data-theme="b" href="/menu.user.php"><?php echo $_smarty_tpl->getVariable('user')->value->login;?>
</a></li>
    <?php if ($_smarty_tpl->getVariable('user')->value->mail_new_count){?><li><a data-theme="b" href='/my.mail.php?only_unreaded'><?php echo $_smarty_tpl->getVariable('lang')->value->getString("Почта");?>
 +<?php echo $_smarty_tpl->getVariable('user')->value->mail_new_count;?>
</a></li><?php }?>
<?php if ($_smarty_tpl->getVariable('user')->value->friend_new_count){?><li><a data-theme="b" href='/my.friends.php'><?php echo $_smarty_tpl->getVariable('lang')->value->getString("Друзья");?>
 +<?php echo $_smarty_tpl->getVariable('user')->value->friend_new_count;?>
</a></li><?php }?>

<?php }else{ ?>
    <li><a data-theme="b" href="/login.php?return=<?php echo $_smarty_tpl->getVariable('URL')->value;?>
"><?php echo $_smarty_tpl->getVariable('lang')->value->getString("Авторизация");?>
</a></li>
    <li><a data-theme="b" href="/reg.php?return=<?php echo $_smarty_tpl->getVariable('URL')->value;?>
"><?php echo $_smarty_tpl->getVariable('lang')->value->getString("Регистрация");?>
</a></li>
    <?php }?>
    </ul>
</div>
