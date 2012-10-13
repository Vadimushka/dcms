<?php /* Smarty version Smarty-3.0.6, created on 2012-10-13 13:39:36
         compiled from "E:/DESURE/Dropbox/domains/dcms7dev/sys/themes/web_blue/tpl/listing.tpl" */ ?>
<?php /*%%SmartyHeaderCode:7941507936d8732348-98455604%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '2426c1f5e5ef2bfad4191ee9da8d62c0adeff26f' => 
    array (
      0 => 'E:/DESURE/Dropbox/domains/dcms7dev/sys/themes/web_blue/tpl/listing.tpl',
      1 => 1347785722,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '7941507936d8732348-98455604',
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
    <?php echo $_smarty_tpl->getVariable('content')->value;?>

</div>