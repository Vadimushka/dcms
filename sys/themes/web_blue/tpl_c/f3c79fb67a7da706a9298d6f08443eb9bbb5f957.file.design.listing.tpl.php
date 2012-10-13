<?php /* Smarty version Smarty-3.0.6, created on 2012-09-16 19:23:35
         compiled from "/var/www/dcmssu2462/data/www/dcms.su/sys/themes/web_blue/tpl/design.listing.tpl" */ ?>
<?php /*%%SmartyHeaderCode:15828618945055eef7121784-98614303%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'f3c79fb67a7da706a9298d6f08443eb9bbb5f957' => 
    array (
      0 => '/var/www/dcmssu2462/data/www/dcms.su/sys/themes/web_blue/tpl/design.listing.tpl',
      1 => 1339315088,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '15828618945055eef7121784-98614303',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<script charset="utf-8" src="<?php echo $_smarty_tpl->getVariable('path')->value;?>
/listing.js" type="text/javascript"></script> 
<?php if ($_smarty_tpl->getVariable('sortable')->value){?>
    <script>
        sortable( '<?php echo $_smarty_tpl->getVariable('sortable')->value;?>
');
    </script>
<?php }?>

<div class="listing">
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
        <?php $_smarty_tpl->tpl_vars['id'] = new Smarty_variable($_smarty_tpl->getVariable('post')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['id'], null, null);?>
        <?php $_smarty_tpl->tpl_vars['url'] = new Smarty_variable($_smarty_tpl->getVariable('post')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['url'], null, null);?>
        <?php $_smarty_tpl->tpl_vars['title'] = new Smarty_variable($_smarty_tpl->getVariable('post')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['title'], null, null);?>
        <?php $_smarty_tpl->tpl_vars['content'] = new Smarty_variable($_smarty_tpl->getVariable('post')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['post'], null, null);?>
        <?php $_smarty_tpl->tpl_vars['bottom'] = new Smarty_variable($_smarty_tpl->getVariable('post')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['edit'], null, null);?>

        <?php $_smarty_tpl->tpl_vars['is_new'] = new Smarty_variable($_smarty_tpl->getVariable('post')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['new'], null, null);?>
        <?php $_smarty_tpl->tpl_vars['is_hide'] = new Smarty_variable($_smarty_tpl->getVariable('post')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['hide'], null, null);?>

        <?php $_smarty_tpl->tpl_vars['actions'] = new Smarty_variable($_smarty_tpl->getVariable('post')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['act'], null, null);?>

        <?php $_smarty_tpl->tpl_vars['icon_size'] = new Smarty_variable($_smarty_tpl->getVariable('post')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['icon']['size'], null, null);?>
        <?php $_smarty_tpl->tpl_vars['icon_src'] = new Smarty_variable($_smarty_tpl->getVariable('post')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['icon']['src'], null, null);?>

        <?php $_smarty_tpl->tpl_vars['checkbox'] = new Smarty_variable($_smarty_tpl->getVariable('post')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['checkbox'], null, null);?>
        <?php $_smarty_tpl->tpl_vars['checkbox_checked'] = new Smarty_variable($_smarty_tpl->getVariable('post')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['checkbox']['checked'], null, null);?>
        <?php $_smarty_tpl->tpl_vars['checkbox_name'] = new Smarty_variable($_smarty_tpl->getVariable('post')->value[$_smarty_tpl->getVariable('smarty')->value['section']['i']['index']]['checkbox']['name'], null, null);?>

    <?php if ($_smarty_tpl->getVariable('id')->value){?><div id="<?php echo $_smarty_tpl->getVariable('id')->value;?>
" class="sortable"><?php }?>

    <?php if ($_smarty_tpl->getVariable('url')->value){?><a href="<?php echo $_smarty_tpl->getVariable('url')->value;?>
"><?php }?>
        <div class="<?php if ($_smarty_tpl->getVariable('is_new')->value){?>post_new<?php }else{ ?>post<?php }?>">
            <table <?php if ($_smarty_tpl->getVariable('is_hide')->value){?>class="post_hide"<?php }?> cellspacing="0" callpadding="0" width="100%">

                <?php if ($_smarty_tpl->getVariable('checkbox')->value){?>
                    <tr class="post_title"><td><label><input type="checkbox"<?php if ($_smarty_tpl->getVariable('checkbox_checked')->value){?> checked="checked"<?php }?> name="<?php echo $_smarty_tpl->getVariable('checkbox_name')->value;?>
" value="1" /><?php echo $_smarty_tpl->getVariable('title')->value;?>
</label>
                            <?php }elseif($_smarty_tpl->getVariable('icon_size')->value=='small'){?>
                    <tr class="post_title"><td><img class="icon_small" src="<?php echo $_smarty_tpl->getVariable('icon_src')->value;?>
" alt="" /> <?php echo $_smarty_tpl->getVariable('title')->value;?>

                        <?php }elseif($_smarty_tpl->getVariable('icon_size')->value=='big'){?>
                    <tr><td class="icon_big" rowspan="4"><img src="<?php echo $_smarty_tpl->getVariable('icon_src')->value;?>
" alt="" /></td><td><?php echo $_smarty_tpl->getVariable('title')->value;?>

                        <?php }else{ ?>
                    <tr class="post_title"><td><?php echo $_smarty_tpl->getVariable('title')->value;?>

                        <?php }?>
                    </td>
                    <?php unset($_smarty_tpl->tpl_vars['smarty']->value['section']['z']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['z']['name'] = 'z';
$_smarty_tpl->tpl_vars['smarty']->value['section']['z']['loop'] = is_array($_loop=$_smarty_tpl->getVariable('actions')->value) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
                        <td class="act"><a href="<?php echo $_smarty_tpl->getVariable('actions')->value[$_smarty_tpl->getVariable('smarty')->value['section']['z']['index']][1];?>
"><img src="/sys/images/icons/act.<?php echo $_smarty_tpl->getVariable('actions')->value[$_smarty_tpl->getVariable('smarty')->value['section']['z']['index']][0];?>
.png" alt="<?php echo $_smarty_tpl->getVariable('actions')->value[$_smarty_tpl->getVariable('smarty')->value['section']['z']['index']][0];?>
" /></a></td>
                            <?php endfor; endif; ?>
                            <?php if ($_smarty_tpl->getVariable('url_end')->value){?>
                        <td rowspan="100%">
                            <a class="url_end" href="<?php echo $_smarty_tpl->getVariable('url_end')->value;?>
"><img src="<?php echo $_smarty_tpl->getVariable('path')->value;?>
/for_css/url_end.png" alt="" /></a>
                        </td>
                    <?php }?>
                </tr>   




                <?php if ($_smarty_tpl->getVariable('content')->value){?>
                    <tr><td class="post_content" colspan="10"><?php echo $_smarty_tpl->getVariable('content')->value;?>
</td></tr>
                <?php }?>

                <?php if ($_smarty_tpl->getVariable('bottom')->value){?>
                    <tr><td class="post_bottom" colspan="10"><?php echo $_smarty_tpl->getVariable('bottom')->value;?>
</td></tr>
                <?php }?>


            </table>
        </div>
    <?php if ($_smarty_tpl->getVariable('url')->value){?></a><?php }?>  
<?php if ($_smarty_tpl->getVariable('id')->value){?></div><?php }?>
<?php endfor; endif; ?>
</div>