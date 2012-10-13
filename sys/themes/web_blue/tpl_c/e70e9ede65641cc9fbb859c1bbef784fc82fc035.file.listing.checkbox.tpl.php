<?php /* Smarty version Smarty-3.0.6, created on 2012-09-17 19:02:29
         compiled from "/var/www/dcmssu2462/data/www/dcms.su/sys/themes/web_blue/tpl/listing.checkbox.tpl" */ ?>
<?php /*%%SmartyHeaderCode:5175810150573b85925055-93354802%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'e70e9ede65641cc9fbb859c1bbef784fc82fc035' => 
    array (
      0 => '/var/www/dcmssu2462/data/www/dcms.su/sys/themes/web_blue/tpl/listing.checkbox.tpl',
      1 => 1347785727,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '5175810150573b85925055-93354802',
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
        <td class="post_time"><?php echo $_smarty_tpl->getVariable('time')->value;?>
</td>
    <?php }?>
<?php  $_smarty_tpl->assign("post_time", ob_get_contents()); Smarty::$_smarty_vars['capture']['default']=ob_get_clean();?>

<?php ob_start(); ?>
    <?php if ($_smarty_tpl->getVariable('counter')->value){?>
        <td class="post_counter"><?php echo $_smarty_tpl->getVariable('counter')->value;?>
</td>
    <?php }?>
<?php  $_smarty_tpl->assign("post_counter", ob_get_contents()); Smarty::$_smarty_vars['capture']['default']=ob_get_clean();?>

<?php ob_start(); ?>
    <?php if ($_smarty_tpl->getVariable('checked')->value){?>
        checked="checked"
    <?php }?>
<?php  $_smarty_tpl->assign("checked_st", ob_get_contents()); Smarty::$_smarty_vars['capture']['default']=ob_get_clean();?>


<div id="<?php echo $_smarty_tpl->getVariable('id')->value;?>
" class="sortable">
    <label for="<?php echo $_smarty_tpl->getVariable('name')->value;?>
">
        <div class="<?php echo $_smarty_tpl->getVariable('div')->value;?>
">
            <table cellspacing="0" callpadding="0" width="100%">

                <tr>
                    <td style="width:16px">
                        <input type="checkbox" id="<?php echo $_smarty_tpl->getVariable('name')->value;?>
" name="<?php echo $_smarty_tpl->getVariable('name')->value;?>
" <?php echo $_smarty_tpl->getVariable('checked_st')->value;?>
 />
                    </td>
                    <td class="post_title">
                        <?php echo $_smarty_tpl->getVariable('title')->value;?>

                    </td>
                    <?php echo $_smarty_tpl->getVariable('post_time')->value;?>

                    <?php echo $_smarty_tpl->getVariable('post_counter')->value;?>

                </tr>


                <?php if ($_smarty_tpl->getVariable('content')->value){?>
                    <tr>
                        <td class="post_content" colspan="10">
                            <?php echo $_smarty_tpl->getVariable('content')->value;?>

                        </td>
                    </tr>
                <?php }?>

                <?php if ($_smarty_tpl->getVariable('bottom')->value){?>
                    <tr>
                        <td class="post_bottom" colspan="10">
                            <?php echo $_smarty_tpl->getVariable('bottom')->value;?>

                        </td>
                    </tr>
                <?php }?>
            </table>
        </div>
    </label>
</div>