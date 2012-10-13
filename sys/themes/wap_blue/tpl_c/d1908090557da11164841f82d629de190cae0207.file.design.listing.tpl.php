<?php /* Smarty version Smarty-3.0.6, created on 2012-09-16 21:17:42
         compiled from "/var/www/dcmssu2462/data/www/dcms.su/sys/themes/wap_blue/tpl/design.listing.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1837857360505609b6058634-85003253%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd1908090557da11164841f82d629de190cae0207' => 
    array (
      0 => '/var/www/dcmssu2462/data/www/dcms.su/sys/themes/wap_blue/tpl/design.listing.tpl',
      1 => 1339315078,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1837857360505609b6058634-85003253',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php unset($_smarty_tpl->tpl_vars['smarty']->value['section']['i']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['name'] = 'i';
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop'] = is_array($_loop=$_smarty_tpl->getVariable('post')->value) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
<div class="post_<?php if (!(1 & $_smarty_tpl->getVariable('smarty')->value['section']['i']['iteration'])){?>0<?php }else{ ?>1<?php }?>">
<?php if ($_smarty_tpl->getVariable('post')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['new']){?><div class="post_new"><?php }?>

<table width="100%">
<?php if ($_smarty_tpl->getVariable('post')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['icon']['size']=='big'){?>
<tr>
<td class="icon_big" rowspan="3"><img src="<?php echo $_smarty_tpl->getVariable('post')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['icon']['src'];?>
" alt="" /></td><td>
<?php echo $_smarty_tpl->getVariable('post')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['title'];?>

<?php }elseif($_smarty_tpl->getVariable('post')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['icon']['size']=='small'){?>
<tr class="post_title"><td>
<img class="icon_small" src="<?php echo $_smarty_tpl->getVariable('post')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['icon']['src'];?>
" alt="" />
<?php echo $_smarty_tpl->getVariable('post')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['title'];?>

<?php }elseif($_smarty_tpl->getVariable('post')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['checkbox']){?>
<tr class="post_title"><td>
<label><input type="checkbox"<?php if ($_smarty_tpl->getVariable('post')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['checkbox']['checked']){?> checked="checked"<?php }?> name="<?php echo $_smarty_tpl->getVariable('post')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['checkbox']['name'];?>
" value="1" />
<?php echo $_smarty_tpl->getVariable('post')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['title'];?>
</label>
<?php }else{ ?>
<tr class="post_title"><td><?php echo $_smarty_tpl->getVariable('post')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['title'];?>

<?php }?>
</td>
</tr>
<?php if ($_smarty_tpl->getVariable('post')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['post']){?>
<tr>
<td colspan="10">
<?php if ($_smarty_tpl->getVariable('post')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['hide']){?><div class="post_hide"><?php }?>
<?php echo $_smarty_tpl->getVariable('post')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['post'];?>

<?php if ($_smarty_tpl->getVariable('post')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['edit']){?><div class="post_edit"><?php echo $_smarty_tpl->getVariable('post')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['edit'];?>
</div><?php }?>
<?php if ($_smarty_tpl->getVariable('post')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['hide']){?></div><?php }?>
</td>
</tr>
<?php }?>
</table>
<?php if ($_smarty_tpl->getVariable('post')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['new']){?></div><?php }?>

<?php if ($_smarty_tpl->getVariable('post')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['act']){?>
<?php unset($_smarty_tpl->tpl_vars['smarty']->value['section']['z']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['z']['name'] = 'z';
$_smarty_tpl->tpl_vars['smarty']->value['section']['z']['loop'] = is_array($_loop=$_smarty_tpl->getVariable('post')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['act']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$_smarty_tpl->tpl_vars['smarty']->value['section']['z']['show'] = true;
$_smarty_tpl->tpl_vars['smarty']->value['section']['z']['max'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['z']['loop'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['z']['step'] = 1;
$_smarty_tpl->tpl_vars['smarty']->value['section']['z']['start'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['z']['step'] > 0 ? 0 : $_smarty_tpl->tpl_vars['smarty']->value['section']['z']['loop']-1;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['z']['show']) {
    $_smarty_tpl->tpl_vars['smarty']->value['section']['z']['total'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['z']['loop'];
    if ($_smarty_tpl->tpl_vars['smarty']->value['section']['z']['total'] == 0)
        $_smarty_tpl->tpl_vars['smarty']->value['section']['z']['show'] = false;
} else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['z']['total'] = 0;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['z']['show']):

            for ($_smarty_tpl->tpl_vars['smarty']->value['section']['z']['index'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['z']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['z']['iteration'] = 1;
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['z']['iteration'] <= $_smarty_tpl->tpl_vars['smarty']->value['section']['z']['total'];
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['z']['index'] += $_smarty_tpl->tpl_vars['smarty']->value['section']['z']['step'], $_smarty_tpl->tpl_vars['smarty']->value['section']['z']['iteration']++):
$_smarty_tpl->tpl_vars['smarty']->value['section']['z']['rownum'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['z']['iteration'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['z']['index_prev'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['z']['index'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['z']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['z']['index_next'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['z']['index'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['z']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['z']['first']      = ($_smarty_tpl->tpl_vars['smarty']->value['section']['z']['iteration'] == 1);
$_smarty_tpl->tpl_vars['smarty']->value['section']['z']['last']       = ($_smarty_tpl->tpl_vars['smarty']->value['section']['z']['iteration'] == $_smarty_tpl->tpl_vars['smarty']->value['section']['z']['total']);
?>
[<a href="<?php echo $_smarty_tpl->getVariable('post')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['act'][$_smarty_tpl->getVariable('smarty')->value['section']['z']['index']][1];?>
"><?php echo $_smarty_tpl->getVariable('post')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['act'][$_smarty_tpl->getVariable('smarty')->value['section']['z']['index']][0];?>
</a>] 
<?php endfor; endif; ?>
<?php }?>

</div>
<?php endfor; endif; ?>