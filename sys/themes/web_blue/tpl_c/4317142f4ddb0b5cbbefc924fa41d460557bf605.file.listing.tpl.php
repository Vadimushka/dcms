<?php /* Smarty version Smarty-3.0.6, created on 2012-09-16 19:13:49
         compiled from "/var/www/dcmssu2462/data/www/dcms.su/sys/themes/web_blue/tpl/listing.tpl" */ ?>
<?php /*%%SmartyHeaderCode:11369426365055ecad47a896-12613763%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '4317142f4ddb0b5cbbefc924fa41d460557bf605' => 
    array (
      0 => '/var/www/dcmssu2462/data/www/dcms.su/sys/themes/web_blue/tpl/listing.tpl',
      1 => 1347785723,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '11369426365055ecad47a896-12613763',
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