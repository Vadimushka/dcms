<?php /* Smarty version Smarty-3.0.6, created on 2012-09-16 21:14:52
         compiled from "/var/www/dcmssu2462/data/www/dcms.su/sys/themes/wap_blue/tpl/listing.post.tpl" */ ?>
<?php /*%%SmartyHeaderCode:3769807385056090c7d0b78-00403890%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '8adb1340ee2f4960e9e54cc99bb404230f79c5e6' => 
    array (
      0 => '/var/www/dcmssu2462/data/www/dcms.su/sys/themes/wap_blue/tpl/listing.post.tpl',
      1 => 1339315080,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '3769807385056090c7d0b78-00403890',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_smarty_tpl->getVariable('hightlight')->value){?>
    <?php $_smarty_tpl->tpl_vars["div"] = new Smarty_variable("post_hightlight", null, null);?>
<?php }else{ ?>
    <?php $_smarty_tpl->tpl_vars["div"] = new Smarty_variable("post", null, null);?>
<?php }?>


<?php ob_start(); ?>
    <?php if ($_smarty_tpl->getVariable('time')->value){?>
        <span class="post_time">(<?php echo $_smarty_tpl->getVariable('time')->value;?>
)</span>
    <?php }?>
<?php  $_smarty_tpl->assign("post_time", ob_get_contents()); Smarty::$_smarty_vars['capture']['default']=ob_get_clean();?>

<?php ob_start(); ?>
    <?php if ($_smarty_tpl->getVariable('counter')->value){?>
        <span class="post_counter"><?php echo $_smarty_tpl->getVariable('counter')->value;?>
</span>
    <?php }?>
<?php  $_smarty_tpl->assign("post_counter", ob_get_contents()); Smarty::$_smarty_vars['capture']['default']=ob_get_clean();?>

<?php ob_start(); ?>
    <?php unset($_smarty_tpl->tpl_vars['smarty']->value['section']['i']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['name'] = 'i';
$_smarty_tpl->tpl_vars['smarty']->value['section']['i']['loop'] = is_array($_loop=$_smarty_tpl->getVariable('actions')->value) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
        <span class="post_action"><a href="<?php echo $_smarty_tpl->getVariable('actions')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['url'];?>
"><img src="<?php echo $_smarty_tpl->getVariable('actions')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['icon'];?>
" alt="" /></a></span>
            <?php endfor; endif; ?>
        <?php  $_smarty_tpl->assign("post_actions", ob_get_contents()); Smarty::$_smarty_vars['capture']['default']=ob_get_clean();?>


<?php if ($_smarty_tpl->getVariable('image')->value){?>

    <img class="post_image" src="<?php echo $_smarty_tpl->getVariable('image')->value;?>
" alt="" />

<?php }?>

<div class="<?php echo $_smarty_tpl->getVariable('div')->value;?>
">
    <div class="post_title">
        <?php if ($_smarty_tpl->getVariable('icon')->value){?>
            <img src="<?php echo $_smarty_tpl->getVariable('icon')->value;?>
" alt="" />
        <?php }?>
    <?php if ($_smarty_tpl->getVariable('url')->value){?><a href="<?php echo $_smarty_tpl->getVariable('url')->value;?>
"><?php }?><?php echo $_smarty_tpl->getVariable('title')->value;?>
<?php if ($_smarty_tpl->getVariable('url')->value){?></a><?php }?>
    <?php echo $_smarty_tpl->getVariable('post_time')->value;?>

    <?php echo $_smarty_tpl->getVariable('post_counter')->value;?>

</div>
<div class="post_content">
    <?php echo $_smarty_tpl->getVariable('content')->value;?>

</div>
<div class="post_bottom">
    <?php echo $_smarty_tpl->getVariable('bottom')->value;?>

</div>
</div>
