<?php /* Smarty version Smarty-3.0.6, created on 2012-09-16 21:14:20
         compiled from "/var/www/dcmssu2462/data/www/dcms.su/sys/themes/pda_blue/tpl/listing.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1575932479505608ec7431a2-21587983%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '52f42d93935029793ddc10f0b140f9a1b44856c8' => 
    array (
      0 => '/var/www/dcmssu2462/data/www/dcms.su/sys/themes/pda_blue/tpl/listing.tpl',
      1 => 1339315072,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1575932479505608ec7431a2-21587983',
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