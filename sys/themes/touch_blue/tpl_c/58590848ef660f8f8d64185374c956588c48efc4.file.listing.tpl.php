<?php /* Smarty version Smarty-3.0.6, created on 2012-11-02 20:12:31
         compiled from "E:/DESURE/Dropbox/domains/dcms7dev/sys/themes/pda_blue/tpl/listing.tpl" */ ?>
<?php /*%%SmartyHeaderCode:281625093f0ef5d5f34-84991534%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '58590848ef660f8f8d64185374c956588c48efc4' => 
    array (
      0 => 'E:/DESURE/Dropbox/domains/dcms7dev/sys/themes/pda_blue/tpl/listing.tpl',
      1 => 1339315072,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '281625093f0ef5d5f34-84991534',
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