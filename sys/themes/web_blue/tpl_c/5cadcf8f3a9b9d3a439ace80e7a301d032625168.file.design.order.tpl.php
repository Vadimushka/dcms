<?php /* Smarty version Smarty-3.0.6, created on 2012-09-16 19:14:56
         compiled from "/var/www/dcmssu2462/data/www/dcms.su/sys/themes/web_blue/tpl/design.order.tpl" */ ?>
<?php /*%%SmartyHeaderCode:17985781815055ecf06749d5-26333921%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '5cadcf8f3a9b9d3a439ace80e7a301d032625168' => 
    array (
      0 => '/var/www/dcmssu2462/data/www/dcms.su/sys/themes/web_blue/tpl/design.order.tpl',
      1 => 1347785727,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '17985781815055ecf06749d5-26333921',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<div class="form">
<select onchange="location = this.options[this.selectedIndex].value;">
<?php unset($_smarty_tpl->tpl_vars['smarty']->value['section']['i']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['name'] = 'i';
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop'] = is_array($_loop=$_smarty_tpl->getVariable('order')->value) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
<?php if ($_smarty_tpl->getVariable('order')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']][2]){?>
 <option value="<?php echo $_smarty_tpl->getVariable('order')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']][0];?>
" selected="selected"><?php echo $_smarty_tpl->getVariable('order')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']][1];?>
</option>
<?php }else{ ?>
 <option value="<?php echo $_smarty_tpl->getVariable('order')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']][0];?>
"><?php echo $_smarty_tpl->getVariable('order')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']][1];?>
</option>
<?php }?>
<?php endfor; endif; ?>
</select>
</div>