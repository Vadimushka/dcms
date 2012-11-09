<?php /* Smarty version Smarty-3.0.6, created on 2012-11-08 19:28:17
         compiled from "E:/DESURE/Dropbox/domains/dcms7dev/sys/themes/touch_blue/tpl/design.pages.tpl" */ ?>
<?php /*%%SmartyHeaderCode:6540509bcf91e72f29-30877224%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'bb600b05e9032e0bcbd3cbace7dde7446fb83364' => 
    array (
      0 => 'E:/DESURE/Dropbox/domains/dcms7dev/sys/themes/touch_blue/tpl/design.pages.tpl',
      1 => 1352388494,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '6540509bcf91e72f29-30877224',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<div class="pages">
<?php if ($_smarty_tpl->getVariable('page')->value==1){?>
<span>1</span>
<?php }else{ ?>
<a href="<?php echo $_smarty_tpl->getVariable('link')->value;?>
page=1">1</a>
<?php }?>
<?php if ($_smarty_tpl->getVariable('page')->value>4){?>
..
<?php }?>
<?php unset($_smarty_tpl->tpl_vars['smarty']->value['section']['page']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['page']['name'] = 'page';
$_smarty_tpl->tpl_vars['smarty']->value['section']['page']['loop'] = is_array($_loop=$_smarty_tpl->getVariable('k_page')->value) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$_smarty_tpl->tpl_vars['smarty']->value['section']['page']['show'] = true;
$_smarty_tpl->tpl_vars['smarty']->value['section']['page']['max'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['page']['loop'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['page']['step'] = 1;
$_smarty_tpl->tpl_vars['smarty']->value['section']['page']['start'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['page']['step'] > 0 ? 0 : $_smarty_tpl->tpl_vars['smarty']->value['section']['page']['loop']-1;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['page']['show']) {
    $_smarty_tpl->tpl_vars['smarty']->value['section']['page']['total'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['page']['loop'];
    if ($_smarty_tpl->tpl_vars['smarty']->value['section']['page']['total'] == 0)
        $_smarty_tpl->tpl_vars['smarty']->value['section']['page']['show'] = false;
} else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['page']['total'] = 0;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['page']['show']):

            for ($_smarty_tpl->tpl_vars['smarty']->value['section']['page']['index'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['page']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['page']['iteration'] = 1;
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['page']['iteration'] <= $_smarty_tpl->tpl_vars['smarty']->value['section']['page']['total'];
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['page']['index'] += $_smarty_tpl->tpl_vars['smarty']->value['section']['page']['step'], $_smarty_tpl->tpl_vars['smarty']->value['section']['page']['iteration']++):
$_smarty_tpl->tpl_vars['smarty']->value['section']['page']['rownum'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['page']['iteration'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['page']['index_prev'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['page']['index'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['page']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['page']['index_next'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['page']['index'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['page']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['page']['first']      = ($_smarty_tpl->tpl_vars['smarty']->value['section']['page']['iteration'] == 1);
$_smarty_tpl->tpl_vars['smarty']->value['section']['page']['last']       = ($_smarty_tpl->tpl_vars['smarty']->value['section']['page']['iteration'] == $_smarty_tpl->tpl_vars['smarty']->value['section']['page']['total']);
?>
<?php if ($_smarty_tpl->getVariable('smarty')->value['section']['page']['iteration']>1&&$_smarty_tpl->getVariable('smarty')->value['section']['page']['iteration']<$_smarty_tpl->getVariable('k_page')->value&&$_smarty_tpl->getVariable('smarty')->value['section']['page']['iteration']<=$_smarty_tpl->getVariable('page')->value+3&&$_smarty_tpl->getVariable('smarty')->value['section']['page']['iteration']>=$_smarty_tpl->getVariable('page')->value-2){?>
<?php if ($_smarty_tpl->getVariable('page')->value==$_smarty_tpl->getVariable('smarty')->value['section']['page']['iteration']){?>
<span><?php echo $_smarty_tpl->getVariable('smarty')->value['section']['page']['iteration'];?>
</span>
<?php }else{ ?>
<a href="<?php echo $_smarty_tpl->getVariable('link')->value;?>
page=<?php echo $_smarty_tpl->getVariable('smarty')->value['section']['page']['iteration'];?>
"><?php echo $_smarty_tpl->getVariable('smarty')->value['section']['page']['iteration'];?>
</a>
<?php }?>
<?php }?>
<?php endfor; endif; ?>
<?php if ($_smarty_tpl->getVariable('page')->value<$_smarty_tpl->getVariable('k_page')->value-4){?>
..
<?php }?>
<?php if ($_smarty_tpl->getVariable('page')->value==$_smarty_tpl->getVariable('k_page')->value){?>
<span><?php echo $_smarty_tpl->getVariable('k_page')->value;?>
</span>
<?php }else{ ?>
<a href="<?php echo $_smarty_tpl->getVariable('link')->value;?>
page=<?php echo $_smarty_tpl->getVariable('k_page')->value;?>
"><?php echo $_smarty_tpl->getVariable('k_page')->value;?>
</a>
<?php }?>
</div>
