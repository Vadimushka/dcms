<?php /* Smarty version Smarty-3.0.6, created on 2012-11-05 17:32:12
         compiled from "E:/DESURE/Dropbox/domains/dcms7dev/sys/themes/touch_blue/tpl/votes.tpl" */ ?>
<?php /*%%SmartyHeaderCode:285275097bfdc765a91-48748318%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '4117f33984a8bb6013196180ac109369b0880963' => 
    array (
      0 => 'E:/DESURE/Dropbox/domains/dcms7dev/sys/themes/touch_blue/tpl/votes.tpl',
      1 => 1352122271,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '285275097bfdc765a91-48748318',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<div class="vote">
<div class="vote_name">
<?php echo output_text($_smarty_tpl->getVariable('name')->value);?>

</div>

<table border="0" cellpadding="0" cellspacing="0" width="100%">
<?php unset($_smarty_tpl->tpl_vars['smarty']->value['section']['i']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['name'] = 'i';
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop'] = is_array($_loop=$_smarty_tpl->getVariable('votes')->value) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
<tr>
<td colspan="2">
<?php echo for_value($_smarty_tpl->getVariable('votes')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['name']);?>
 <?php if ($_smarty_tpl->getVariable('votes')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['count']){?>(<?php echo $_smarty_tpl->getVariable('votes')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['count'];?>
)<?php }?>
</td>
</tr>
<tr>
<td class="votes">
<div class="votes" style="width:<?php echo $_smarty_tpl->getVariable('votes')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['pc'];?>
%;">
<?php echo $_smarty_tpl->getVariable('votes')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['pc'];?>
%
</div>
</td>
<td class="votes_add">
<?php if ($_smarty_tpl->getVariable('is_add')->value){?>
<a href="<?php echo $_smarty_tpl->getVariable('votes')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['url'];?>
">+</a>
<?php }?>
</td>

</tr>
<?php endfor; endif; ?>
</table>

</div>
